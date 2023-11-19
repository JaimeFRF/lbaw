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

const removeItemCart = document.getElementById('removeItem');

removeItemCart.addEventListener('click', function(e) {

    e.preventDefault();
    fetch('CartItemControlller.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        console.log(data);
        // Handle the response data
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
});

