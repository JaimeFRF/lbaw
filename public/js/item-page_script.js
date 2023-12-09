function addItemToCart(product) {
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
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
})
};



