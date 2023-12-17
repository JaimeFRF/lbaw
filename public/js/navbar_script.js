window.onload = function() {
    updateNavbar();
};

function updateNavbar() {
    
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
        document.getElementById("ItemCartNumber").innerText = "(" + data.count +")";
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
}