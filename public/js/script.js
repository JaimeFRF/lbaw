document.addEventListener('DOMContentLoaded', function() {
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');

    togglePassword.addEventListener('click', function(e) {
        // Toggle the type attribute between 'text' and 'password'
        const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordField.setAttribute('type', type);

        // Toggle the button text/content
        togglePassword.getAttribute('class') === 'bi bi-eye-slash' ? togglePassword.setAttribute('class', 'bi bi-eye') : togglePassword.setAttribute('class', 'bi bi-eye-slash');
    });
});


