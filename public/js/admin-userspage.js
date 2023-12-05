document.addEventListener('DOMContentLoaded', function() {
    const banButtons = document.querySelectorAll('[id="ban"]');
    const deleteButtons = document.querySelectorAll('[id="delete"]');
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    banButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            let userID = this.getAttribute('data-user-id');
            let status = (this.closest('tr').querySelector('#status').innerText == "Active" ? 0 : 1);
            event.preventDefault();
            fetch('/admin-ban-user/' + userID, { 
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token, 
                },
                body: JSON.stringify({
                    'userID' : userID,
                }),
            }).then(response => {
                if (response.ok && response.status === 200) {
                  return response.json();
                } else {
                  throw new Error('Something went wrong');
                }
              })
            .then(data => {
                    this.closest('tr').querySelector('#status').innerText = (this.closest('tr').querySelector('#status').innerText == "Active" ? "Banned" : "Active") ;
            }).catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
            });
        });
    });
    deleteButtons.forEach(button  => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            let userID = this.getAttribute('data-user-id');
            fetch('/admin-delete-user/' + userID, { 
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token, 
                },
                body: JSON.stringify({
                    'userID' : userID,
                }),
            }).then(response => {
                if (response.ok && response.status === 200) {
                  return response.json();
                } else {
                  throw new Error('Something went wrong');
                }
              })
            .then(data => {
                console.log(this);
                    this.closest('tr').remove();
                }
            ).catch(error => {
                console.error('There has been a problem with your fetch operation:', error);
            });

        });
    });
});
