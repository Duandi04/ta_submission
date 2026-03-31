/**
 * Auth Scripts for TA Submission System
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── Password visibility toggle ──────────────────────────────────────
    const togglePassword = document.querySelector('#togglePassword');
    const passwordInput  = document.querySelector('#password');
    const toggleIcon     = document.querySelector('#toggleIcon');

    if (togglePassword && passwordInput && toggleIcon) {
        togglePassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            toggleIcon.classList.toggle('bi-eye');
            toggleIcon.classList.toggle('bi-eye-slash');
        });
    }

    // ── Login form loading state ────────────────────────────────────────
    const loginForm = document.querySelector('#loginForm');
    const loginBtn  = document.querySelector('#loginBtn');

    if (loginForm && loginBtn) {
        loginForm.addEventListener('submit', function () {
            // Brief timeout so browser validation fires first
            requestAnimationFrame(() => {
                if (loginForm.checkValidity()) {
                    loginBtn.disabled = true;
                    loginBtn.querySelector('.btn-login-text').classList.add('d-none');
                    loginBtn.querySelector('.btn-login-loading').classList.remove('d-none');
                }
            });
        });
    }

});
