window.onload = function() {
    // Code to update the navbar goes here
    updateNavbar();
};

function updateNavbar() {
    // Example: Fetch and update the cart item count
    
    fetch('/api/cart/count', {
        method: 'GET',
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
        console.log("Item count:", data.count);
        document.getElementById("ItemCartNumber").innerText = "(" + data.count +")";
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
}