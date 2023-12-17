document.addEventListener('DOMContentLoaded', function() {
    var currentOffset = 0;
    var totalItems = window.totalItems; 

    document.querySelector('.next-arrow').addEventListener('click', function() {
        currentOffset += 3;

        if (currentOffset >= totalItems) {
            currentOffset = 0;
        }

        fetch('/next-items/' + currentOffset)
            .then(response => response.text())
            .then(data => {
                document.getElementById('productRow').innerHTML = data;
            });
    });

    document.querySelector('.prev-arrow').addEventListener('click', function() {
        currentOffset -= 3; 

        if (currentOffset < 0) {
            currentOffset = totalItems - 3;
        }

        fetch('/next-items/' + currentOffset)
            .then(response => response.text())
            .then(data => {
                document.getElementById('productRow').innerHTML = data;
            });
    });
});