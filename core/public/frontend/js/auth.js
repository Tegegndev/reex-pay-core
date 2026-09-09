"use strict";

// Toggle Password Visibility (Login Page)
const togglePasswordBtn = document.getElementById('togglePassword');
if (togglePasswordBtn) {
    togglePasswordBtn.addEventListener('click', function () {
        const passwordInput = document.getElementById('password');
        const passwordIcon = this.querySelector('i');

        if (passwordInput) {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';

            if (passwordIcon) {
                passwordIcon.classList.toggle('fa-eye', !isHidden);
                passwordIcon.classList.toggle('fa-eye-slash', isHidden);
            }
        }
    });
}

// Country Select to Phone Code Sync (Register Page)
const countrySelect = document.getElementById('countrySelect');
if (countrySelect) {
    countrySelect.addEventListener('change', function (e) {
        e.preventDefault();
        const country = this.value;
        const phoneCode = country.split(":")[1];

        const phoneElement = document.getElementById('phone');
        if (phoneElement) {
            phoneElement.innerHTML = phoneCode;
        }
    });
}
