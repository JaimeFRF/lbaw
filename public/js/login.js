document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.toggle-password').forEach(toggleIcon => {
        toggleIcon.addEventListener('click', event => {
            const passwordField = event.currentTarget.closest('.input-group').querySelector('input[name="password"]');
            if (!passwordField) return;
            passwordField.type = passwordField.type === 'password' ? 'text' : 'password';
            const icon = event.currentTarget.querySelector('i');
            if (icon) {
                if (passwordField.type === 'password') {
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            }
        });
    });
});
