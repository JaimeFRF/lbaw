document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="rating"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            let rating = this.value;
            let itemId = document.body.dataset.itemId;

            // Update the display
            document.getElementById('selectedRating').innerText = `You rated: ${rating} stars`;

            // Send the rating to the server using AJAX
            sendRatingToServer(rating, itemId);
        });
    });

    function sendRatingToServer(rating, itemId) {
        let url = '/review'; 
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token, 
            },
            body: JSON.stringify({ rating: rating , itemId: itemId}),
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login';
            } else if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.text().then(text => {
                console.log('Raw response:', text);
                return JSON.parse(text);
            });
        })
    }
});