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

    const checkoutButton = document.getElementById('checkoutButton');

    checkoutButton.addEventListener('click', function (e) {
        e.preventDefault();

        Swal.fire({
            title: 'Sign Up or Log In',
            text: 'You need to sign up or log in to complete the checkout.',
            icon: 'info',
            confirmButtonText: 'Go to Login',
            showCancelButton: true,
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = '/login'; 
            }
        });
    });

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
                let items = JSON.parse(document.getElementById('items').value);
                let item = items.find(item => item.id == itemId);
                if (item.pivot && item.pivot.quantity != null) {
                    item.pivot.quantity += newQuantity;
                } else if (item.quantity != null) {
                    item.quantity += newQuantity;
                }
                quantityElement.innerText = data.newQuantity;
                if (data.newQuantity == 0) {
                    const productRow = cartItem.closest('tr'); // Assuming each cart item is in its own table row
                    productRow.remove();
                }
                document.getElementById('total-price').innerText = data.totalPrice + '€';
                document.getElementById('items').value = JSON.stringify(items);
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });


});




