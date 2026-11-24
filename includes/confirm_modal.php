<div id="customConfirmModal"
     role="dialog"
     aria-modal="true"
     aria-labelledby="confirmModalTitle"
     style="display: none; position: fixed; inset: 0; width: 100vw; height: 100vh; background: rgba(15, 23, 42, 0.58); z-index: 99999; align-items: center; justify-content: center; padding: 20px; box-sizing: border-box;">
    <div id="customModalBox"
         style="background: #ffffff; border: 1px solid #e2e8f0; border-top: 4px solid #ef4444; border-radius: 16px; max-width: 420px; width: 100%; padding: 24px; box-shadow: 0 20px 35px rgba(15, 23, 42, 0.25); box-sizing: border-box;">
        <span id="confirmModalBadge"
              style="display: inline-block; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; padding: 4px 10px; border-radius: 999px; margin-bottom: 10px; background: #fef2f2; color: #ef4444;">Konfirmasi</span>
        <h3 id="confirmModalTitle" style="margin: 0 0 8px 0; font-size: 1.15rem; font-weight: 700; color: #1e293b;">Pemberitahuan</h3>
        <p id="confirmModalDesc" style="margin: 0 0 20px 0; font-size: 0.925rem; color: #64748b; line-height: 1.55;">Apakah Anda yakin ingin melanjutkan?</p>
        <div id="confirmModalActions" style="display: flex; justify-content: flex-end; gap: 10px;">
            <button type="button" id="confirmModalCancel" class="btn btn-secondary">Batal</button>
            <button type="button" id="confirmModalOkOnly" class="btn btn-info" style="display: none;">Mengerti</button>
            <a href="#" id="confirmModalAction" class="btn btn-danger">Lanjutkan</a>
        </div>
    </div>
</div>

<script>
(function () {
    const modal = document.getElementById('customConfirmModal');
    const modalBox = document.getElementById('customModalBox');
    const badgeEl = document.getElementById('confirmModalBadge');
    const titleEl = document.getElementById('confirmModalTitle');
    const descEl = document.getElementById('confirmModalDesc');
    const actionsEl = document.getElementById('confirmModalActions');
    const cancelBtn = document.getElementById('confirmModalCancel');
    const okOnlyBtn = document.getElementById('confirmModalOkOnly');
    const actionBtn = document.getElementById('confirmModalAction');
    let pendingForm = null;

    function closeModal() {
        modal.style.display = 'none';
        pendingForm = null;
    }

    window.showAppPopup = function ({
        type = 'delete',
        title = 'Pemberitahuan',
        message = '',
        confirmLabel = '',
        href = '#',
        form = null
    }) {
        pendingForm = form;
        titleEl.textContent = title;
        descEl.textContent = message;

        actionBtn.classList.remove('btn-danger', 'btn-info');
        okOnlyBtn.classList.remove('btn-danger', 'btn-info');
        modalBox.style.borderTop = 'none';
        modalBox.style.borderLeft = 'none';
        modalBox.style.background = '#ffffff';

        if (type === 'info' || type === 'error') {
            const isErr = (type === 'error');
            modalBox.style.background = isErr ? '#fffafa' : '#f8fafc';
            modalBox.style.borderLeft = isErr ? '4px solid #ef4444' : '4px solid #0284c7';
            badgeEl.textContent = isErr ? 'Pemberitahuan Penting' : 'Pemberitahuan';
            badgeEl.style.background = isErr ? '#fee2e2' : '#e0f2fe';
            badgeEl.style.color = isErr ? '#ef4444' : '#0284c7';

            cancelBtn.style.display = 'none';
            actionBtn.style.display = 'none';
            okOnlyBtn.style.display = 'inline-flex';
            actionsEl.style.justifyContent = 'center';

            if (isErr) {
                okOnlyBtn.classList.add('btn-danger');
                okOnlyBtn.textContent = confirmLabel || 'Tutup';
            } else {
                okOnlyBtn.classList.add('btn-info');
                okOnlyBtn.textContent = confirmLabel || 'Mengerti';
            }
        } else {
            const isEdit = (type === 'edit');
            modalBox.style.borderTop = isEdit ? '4px solid #4f46e5' : '4px solid #ef4444';
            badgeEl.textContent = isEdit ? 'Konfirmasi Edit' : 'Konfirmasi Hapus';
            badgeEl.style.background = isEdit ? '#eef2ff' : '#fef2f2';
            badgeEl.style.color = isEdit ? '#4f46e5' : '#ef4444';

            okOnlyBtn.style.display = 'none';
            cancelBtn.style.display = 'inline-flex';
            actionBtn.style.display = 'inline-flex';
            actionsEl.style.justifyContent = 'flex-end';

            if (!isEdit) {
                actionBtn.classList.add('btn-danger');
            }
            actionBtn.textContent = confirmLabel || (isEdit ? 'Ya, Edit' : 'Ya, Hapus');
            if (href) {
                actionBtn.setAttribute('href', href);
            }
        }

        modal.style.display = 'flex';
    };

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest('.js-confirm-trigger');
        if (!trigger) return;

        e.preventDefault();
        const mode = trigger.getAttribute('data-mode') || 'delete';
        const url = trigger.getAttribute('data-url') || trigger.getAttribute('href') || '#';
        const title = trigger.getAttribute('data-title') || (mode === 'edit' ? 'Konfirmasi Edit' : 'Konfirmasi Hapus');
        const message = trigger.getAttribute('data-message') || (mode === 'edit'
            ? 'Yakin ingin mengedit data ini?'
            : 'Yakin ingin menghapus data ini?');
        const label = trigger.getAttribute('data-label') || (mode === 'edit' ? 'Ya, Edit' : 'Ya, Hapus');
        const form = trigger.closest('form');

        window.showAppPopup({
            type: mode,
            title: title,
            message: message,
            confirmLabel: label,
            href: url,
            form: trigger.tagName === 'BUTTON' ? form : null
        });
    });

    actionBtn.addEventListener('click', function (e) {
        if (pendingForm) {
            e.preventDefault();
            const formToSubmit = pendingForm;
            closeModal();
            formToSubmit.submit();
        }
    });

    okOnlyBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
    });

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && modal.style.display === 'flex') {
            closeModal();
        }
    });
})();
</script>