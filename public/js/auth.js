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
            // Once the form is submitted (native validation passed), show loading state
            loginBtn.disabled = true;
            const btnText = loginBtn.querySelector('.btn-login-text');
            const btnLoading = loginBtn.querySelector('.btn-login-loading');
            
            if (btnText) btnText.classList.add('d-none');
            if (btnLoading) btnLoading.classList.remove('d-none');
        });
    }

});
