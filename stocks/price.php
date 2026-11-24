<?php
header('Content-Type: application/json; charset=utf-8');

$symbol = strtoupper(trim($_GET['symbol'] ?? ''));
if ($symbol === '') {
    echo json_encode([
        'ok' => false,
        'message' => 'Simbol aset kosong.'
    ]);
    exit;
}

function httpGetJson(string $url): ?array
{
    $context = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 8,
            'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\nAccept: application/json\r\n"
        ]
    ]);

    $raw = @file_get_contents($url, false, $context);
    if ($raw === false || $raw === '') {
        return null;
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : null;
}

function fetchYahooQuote(string $sym): ?array
{
    $encoded = urlencode($sym);
    $url = "https://query1.finance.yahoo.com/v8/finance/chart/{$encoded}?interval=1d&range=1d";
    $data = httpGetJson($url);

    if (!$data) {
        $url2 = "https://query2.finance.yahoo.com/v8/finance/chart/{$encoded}?interval=1d&range=1d";
        $data = httpGetJson($url2);
    }

    if (!$data || empty($data['chart']['result'][0]['meta'])) {
        return null;
    }

    $meta = $data['chart']['result'][0]['meta'];
    $price = isset($meta['regularMarketPrice']) ? (float) $meta['regularMarketPrice'] : 0.0;
    if ($price <= 0) {
        return null;
    }

    $currency = strtoupper(trim((string) ($meta['currency'] ?? 'USD')));
    return [
        'symbol' => strtoupper((string) ($meta['symbol'] ?? $sym)),
        'price' => $price,
        'currency' => $currency
    ];
}

function getFxRateToIdr(string $fromCurrency): ?float
{
    $from = strtoupper(trim($fromCurrency));
    if ($from === '' || $from === 'IDR') {
        return 1.0;
    }

    // Coba ambil kurs pasangan {FROM}IDR=X dari Yahoo Finance
    $fxQuote = fetchYahooQuote($from . 'IDR=X');
    if ($fxQuote && $fxQuote['price'] > 0) {
        return (float) $fxQuote['price'];
    }

    // Cadangan via API kurs publik (Frankfurter / Open ER)
    $rateApi = httpGetJson("https://open.er-api.com/v6/latest/{$from}");
    if ($rateApi && isset($rateApi['rates']['IDR']) && (float) $rateApi['rates']['IDR'] > 0) {
        return (float) $rateApi['rates']['IDR'];
    }

    return null;
}

$quote = fetchYahooQuote($symbol);

// Jika saham IDX tanpa akhiran .JK dan tidak ketemu, coba tambahkan .JK
if (!$quote && !str_contains($symbol, '.') && !str_contains($symbol, '-') && preg_match('/^[A-Z]{4}$/', $symbol)) {
    $quote = fetchYahooQuote($symbol . '.JK');
}

if (!$quote) {
    echo json_encode([
        'ok' => false,
        'message' => "Harga untuk simbol '{$symbol}' tidak ditemukan."
    ]);
    exit;
}

$rawPrice = (float) $quote['price'];
$originalCurrency = strtoupper((string) $quote['currency']);
$fxRate = getFxRateToIdr($originalCurrency);

if ($fxRate === null || $fxRate <= 0) {
    echo json_encode([
        'ok' => false,
        'message' => "Harga ditemukan dalam {$originalCurrency} ({$rawPrice}), namun kurs ke IDR gagal dimuat."
    ]);
    exit;
}

$priceInIdr = round($rawPrice * $fxRate, 2);

echo json_encode([
    'ok' => true,
    'symbol' => $quote['symbol'],
    'price' => $priceInIdr,
    'original_price' => $rawPrice,
    'original_currency' => $originalCurrency,
    'fx_rate' => $fxRate,
    'currency' => 'IDR'
]);