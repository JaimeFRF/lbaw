document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('#cancel-button');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var orderId = this.getAttribute('data-review-id');
            console.log(orderId);
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
});