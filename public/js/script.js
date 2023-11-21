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


document.addEventListener('DOMContentLoaded', function() {
    document.body.addEventListener('click', function(e) {
        if (e.target.matches('.quantity-btn')) {
            const button = e.target;
            const cartItem = button.closest('.cart-item');
            const itemId = cartItem.dataset.itemId;
            const quantityElement = cartItem.querySelector('.quantity-text');
            let newQuantity = (button.classList.contains('increment') ? 1 : -1);


            fetch('/update-cart-item', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token
                },
                body: JSON.stringify({
                    itemId: itemId,
                    quantity: newQuantity
                })
            })
            .then(response => response.json())
            .then(data => {
                quantityElement.innerText = data.newQuantity;
                if (data.newQuantity == 0) {
                    const productRow = cartItem.closest('tr'); // Assuming each cart item is in its own table row
                    productRow.remove();
                }
                document.getElementById('total-price').innerText = data.totalPrice + '€';
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});




