document.addEventListener('DOMContentLoaded', function() {
    const banButtons = document.querySelectorAll('[id="ban"]');

    banButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            // Find the closest ancestor tr of the clicked button
            let statusCell = this.closest('tr').querySelector('#status').innerText = 'Banned';
            let banBtnText = this.closest('tr').querySelector('#ban').innerText == 'Ban' ? 'bane': 'Ban';
            console.log(closestTr);
            console.log(statusCell);
        });
    });
});
