import '@fortawesome/fontawesome-free/css/all.min.css';
import { showSuccess, showError, showWarning, confirmAction, confirmDelete, confirmLogout } from './modules/sweetalert.js';
import './modules/siteplan.js';
import { initTabs } from './modules/tabs.js';
import { initSearchableSelects } from './modules/searchable-select.js';
import { initBookingForm } from './modules/booking.js';
import { initCommissionSettings } from './modules/commission-settings.js';

document.addEventListener('DOMContentLoaded', () => {
    initTabs();
    initSearchableSelects();
    initBookingForm();
    initCommissionSettings();
    // 1. Process server flash messages
    const flashContainer = document.getElementById('flash-messages');
    if (flashContainer) {
        const successMessage = flashContainer.getAttribute('data-success');
        const errorMessage = flashContainer.getAttribute('data-error');
        const warningMessage = flashContainer.getAttribute('data-warning');

        if (successMessage) {
            showSuccess('Berhasil', successMessage);
        } else if (errorMessage) {
            showError('Perhatian', errorMessage);
        } else if (warningMessage) {
            showWarning('Peringatan', warningMessage);
        }
    }

    // 2. Global listener for logout confirmation
    document.addEventListener('click', (event) => {
        const logoutTrigger = event.target.closest('[data-confirm-logout]');
        if (logoutTrigger) {
            event.preventDefault();
            const formId = logoutTrigger.getAttribute('data-form-id') || 'logout-form';
            const form = document.getElementById(formId);

            confirmLogout().then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        }
    });

    // 3. Global listener for delete confirmations
    document.addEventListener('click', (event) => {
        const deleteTrigger = event.target.closest('[data-confirm-delete]');
        if (deleteTrigger) {
            event.preventDefault();
            const title = deleteTrigger.getAttribute('data-title') || 'Hapus Data?';
            const text = deleteTrigger.getAttribute('data-text') || 'Data ini akan dihapus secara permanen.';
            const form = deleteTrigger.closest('form');

            confirmDelete(title, text).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        }
    });

    // 4. Global listener for generic action confirmations (approve, cancel, etc.)
    document.addEventListener('click', (event) => {
        const actionTrigger = event.target.closest('[data-confirm-action]');
        if (actionTrigger) {
            event.preventDefault();
            const title = actionTrigger.getAttribute('data-title') || 'Konfirmasi Tindakan';
            const text = actionTrigger.getAttribute('data-text') || 'Apakah Anda yakin ingin melanjutkan?';
            const confirmButtonText = actionTrigger.getAttribute('data-confirm-text') || 'Ya, Lanjutkan';
            const isDanger = actionTrigger.getAttribute('data-is-danger') === 'true';
            const form = actionTrigger.closest('form');

            confirmAction({ title, text, confirmButtonText, isDanger }).then((result) => {
                if (result.isConfirmed && form) {
                    form.submit();
                }
            });
        }
    });

    // 5. Quick login helper
    document.addEventListener('click', (event) => {
        const quickTrigger = event.target.closest('[data-quick-login]');
        if (quickTrigger) {
            event.preventDefault();
            const email = quickTrigger.getAttribute('data-email');
            const password = quickTrigger.getAttribute('data-password') || 'password';
            const autoSubmit = quickTrigger.getAttribute('data-auto-submit') === 'true';

            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');
            const loginForm = document.getElementById('login-form');

            if (emailInput && passwordInput) {
                emailInput.value = email;
                passwordInput.value = password;

                if (autoSubmit && loginForm) {
                    loginForm.submit();
                }
            }
        }
    });
});
