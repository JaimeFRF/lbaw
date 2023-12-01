function updateRatingOnWebpage(newRating) {
    var itemRatingElement = document.getElementById('itemRating');
    if (itemRatingElement) {
        itemRatingElement.textContent = newRating;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('input[name="rating"]').forEach((radio) => {
        radio.addEventListener('change', function () {
            let rating = this.value;
            let itemId = document.body.dataset.itemId;
            let reviewText = document.getElementById('reviewText').value;

            if (reviewText.trim() !== '') {
                sendRatingToServer(rating, itemId, reviewText);
            } else {
                console.log('Review text is empty. Ignoring the request.');
            }
        });
    });

    function sendRatingToServer(rating, itemId, reviewText) {
        let url = '/review'; 
        let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token, 
            },
            body: JSON.stringify({ rating: rating, itemId: itemId, review: reviewText }),
        })
        .then(response => {
            if (response.status === 401) {
                window.location.href = '/login';
            } else if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json(); 
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            updateRatingOnWebpage(data.rating);
            displayNewReview(data.reviewText, data.reviewRating, data.username);  
        })
        .catch(error => {
            console.error(error);
            alert(error.message);
        });
    }


    function displayNewReview(reviewText, reviewRating, username) {
        let reviewsSection = document.querySelector('.reviews-section');
        let newReviewDiv = document.createElement('div');
        newReviewDiv.className = 'review';
        newReviewDiv.innerHTML = `
            <hr></hr>
                <div class="review">
                    <div class="review-header">
                        <div class="username">${username}</div>
                        <div class="rating">${'&#9733;'.repeat(reviewRating)}</div>
                    </div>
                    <p>${reviewText}</p>
                </div>
            <hr></hr>

        `;
        reviewsSection.appendChild(newReviewDiv);
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var deleteButtons = document.querySelectorAll('.delete-button');
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            var reviewId = this.getAttribute('data-review-id');

            fetch('/review/delete/' + reviewId, { 
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token, 
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.closest('.review').remove();
                    updateRatingOnWebpage(data.newRating);
                }
            });
        });
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const editButtons = document.querySelectorAll('.edit-button');

    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const reviewDiv = this.closest('.review');
            const description = reviewDiv.querySelector('p');
            const rating = reviewDiv.querySelector('.rating');
            let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');


            const descriptionInput = document.createElement('textarea');
            descriptionInput.textContent = description.textContent;
            description.replaceWith(descriptionInput);


            const ratingDiv = document.createElement('div');
            ratingDiv.className = 'edit-review-stars';
            
            const buttonContainer = document.createElement('div');
            buttonContainer.style.display = 'flex';
            buttonContainer.style.justifyContent = 'center';
            buttonContainer.style.alignItems = 'center';
            
            const increaseButton = document.createElement('button');
            increaseButton.textContent = '+';
            increaseButton.style.margin = '0 5px';
            increaseButton.style.padding = '5px 10px';
            increaseButton.addEventListener('click', function() {
                let currentStars = parseInt(starsDisplay.textContent);
                if (currentStars < 5) {
                    starsDisplay.textContent = currentStars + 1;
                }
            });
            
            const decreaseButton = document.createElement('button');
            decreaseButton.textContent = '-';
            decreaseButton.style.margin = '0 5px';
            decreaseButton.style.padding = '5px 10px';
            decreaseButton.addEventListener('click', function() {
                let currentStars = parseInt(starsDisplay.textContent);
                if (currentStars > 1) {
                    starsDisplay.textContent = currentStars - 1;
                }
            });
            
            const starsDisplay = document.createElement('div');
            starsDisplay.textContent = rating.dataset.rating; 
            
            buttonContainer.appendChild(decreaseButton);
            buttonContainer.appendChild(starsDisplay);
            buttonContainer.appendChild(increaseButton);
            
            ratingDiv.appendChild(buttonContainer);
            
            rating.replaceWith(ratingDiv);

            const saveButton = document.createElement('button');
            saveButton.innerHTML = '<i class="fas fa-save"></i>'; // Diskette icon
            saveButton.className = 'edit-button'; 
            this.replaceWith(saveButton);

            var reviewId = this.getAttribute('data-review-id');


            saveButton.addEventListener('click', function() {
                fetch('/review/edit/' + reviewId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': token, 
                    },
                    body: JSON.stringify({
                        description: descriptionInput.value,
                        rating: starsDisplay.textContent,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    updateRatingOnWebpage(data.newRating);

                    descriptionInput.replaceWith(description);
                    description.textContent = data.description;
            
                    ratingDiv.replaceWith(rating); 
                    rating.textContent = '★'.repeat(data.rating);

                    this.replaceWith(button);

                });
            });
        });
    });
});
