document.addEventListener('DOMContentLoaded', function() {
    function createStarRating(rating) {
        let ratingHtml = '';
        for (let i = 1; i <= 5; i++) {
            if (i <= rating) {
                ratingHtml += '<i class="bi bi-star-fill"></i>'; 
            } else if (i === Math.ceil(rating) && !Number.isInteger(rating)) {
                ratingHtml += '<i class="bi bi-star-half"></i>'; 
            } else {
                ratingHtml += '<i class="bi bi-star"></i>'; 
            }
        }
        return ratingHtml;
    }

    const numericRatingElement = document.getElementById('numeric-rating');
    if (numericRatingElement) {
        const numericRating = parseFloat(numericRatingElement.textContent);
        document.getElementById('star-rating').innerHTML = createStarRating(numericRating);
    }
});


function addItemToCart(product, stock) {
    if (stock <= 0) {
        alert('This item is out of stock');
        return;
    }

    const value = document.getElementById("ItemCartNumber").innerText;
    let match = value.match(/\d+/);
    let number = parseInt(match[0], 10);
    document.getElementById("ItemCartNumber").innerText = "(" + (number + 1) + ")";

    fetch(`/cart/add/${product}`,{
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ itemId: product, quantity: 1 })
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        stock--;
        document.getElementById('addToCart').setAttribute('data-stock', stock);
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    })
};