document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('#cancel-button');
    let detailsButtons = document.querySelectorAll('#detailsButton');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var orderId = this.getAttribute('data-review-id');
            fetch('/purchase/delete/' + orderId, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.order-history').remove();
                }
            });
        });
    });

    detailsButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
    
            let orderNumber = this.getAttribute('data-order-number');
            let date = this.getAttribute('data-date');
            let value = this.getAttribute('data-value');
            let status = this.getAttribute('data-status');
            let address = this.getAttribute('data-address');
            let city = this.getAttribute('data-city');
            let country = this.getAttribute('data-country');
            let postalCode = this.getAttribute('data-postalCode');
            let items = JSON.parse(this.getAttribute('data-items'));

            Swal.fire({
                title: 'Details for Order ' + orderNumber,
                html: 'Date: ' + date + '<br>' + 
                'Value: ' + value + '<br>' + 
                'Status: ' + status + '<br>' +
                'Address: ' + address + '<br>' +
                'City: ' + city + '<br>' +
                'Country: ' + country + '<br>' +
                'Postal Code: ' + postalCode + '<br>' +
                'Items: ' + items.map(item => item.name + ' (' + item.quantity + ')').join(', '),
                icon: 'info',
                confirmButtonText: 'Close',
            });
        });
    });
});

function toggleScroll(contentId) {
    var content = document.getElementById(contentId);
    var seeMoreLink = content.nextElementSibling; 

    if (seeMoreLink.textContent.trim() === 'See more...') {
        content.style.maxHeight = '600px';
        content.style.overflowY = 'auto';  
        seeMoreLink.textContent = 'See less...';
    } 
    else {
        content.style.maxHeight = '450px';   
        content.style.overflowY = 'hidden'; 
        seeMoreLink.textContent = 'See more...';
    }
}

