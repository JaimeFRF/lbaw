document.addEventListener('DOMContentLoaded', function() {
    const banButtons = document.querySelectorAll('[id="ban"]');
    const deleteButtons = document.querySelectorAll('[id="delete"]');

    banButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            this.closest('tr').querySelector('#status').innerText = (this.closest('tr').querySelector('#status').innerText == "Active" ? "Banned" : "Active") ;
        });
    });
    deleteButtons.forEach(button  => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            this.closest('tr').remove();
        });
    });
});
