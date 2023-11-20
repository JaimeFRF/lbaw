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
            let newQuantity = parseInt(quantityElement.innerText) + (button.classList.contains('increment') ? 1 : -1);

            newQuantity = Math.max(newQuantity, 0);

            // Send fetch request to update quantity
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
                console.log(data);
                quantityElement.innerText = data.newQuantity;
                document.getElementById('total-price').innerText = data.totalPrice + '€';
                //document.getElementById('total-price').innerText = 'Total Price: $' + data.totalPrice;
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
});




