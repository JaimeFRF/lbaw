document.addEventListener('DOMContentLoaded', function() {
    const banButtons = document.querySelectorAll('[id="ban"]');
    const deleteButtons = document.querySelectorAll('[id="delete"]');
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const editButtons = document.querySelectorAll('.edit-btn'); 
    const editUserModal = new bootstrap.Modal(document.getElementById('editUserModal'));
    const manualCloseModalButton = document.getElementById('manualCloseModalButton');
    const editUserForm = document.getElementById('editUserForm');
    const updateButton = document.querySelector('.update-user-btn');
    const addUserForm = document.getElementById('addUserForm');
    const addUserModal = new bootstrap.Modal(document.getElementById('addUserModal'));

    addUserForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(addUserForm);

        fetch('/admin-add-user', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
        })
        .then(response => {
            if (response.ok && response.status === 200) {
                return response.json();
            } else {
                throw new Error('Something went wrong');
            }
        })
        .then(data => {
            addUserForm.reset();
            addUserModal.hide();
            Swal.fire('User Added', `User ${data.username} has been added successfully.`, 'success');
            location.reload();

        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
    });


    updateButton.addEventListener('click', function(event) {
        event.preventDefault();

        const userId = document.getElementById('editUserId').value;
        const formData = new FormData(editUserForm);
        formData.append('id_user', userId);

        fetch(`/admin-update-user/${userId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (response.ok && response.status === 200) {
                return response.json();
        } else {
            throw new Error('Something went wrong');
        }
    })
    .then(data => {
        const row = document.querySelector(`tr[data-user-id="${userId}"]`);
        row.cells[1].innerText = data.updatedUserData.name;  
        row.cells[2].innerText = data.updatedUserData.username;  
        row.cells[3].innerText = data.updatedUserData.email;  
        row.cells[4].innerText = data.updatedUserData.phone; 

        editUserModal.hide();

        Swal.fire({
            icon: 'success',
            title: 'User Updated',
            text: 'The user has been updated successfully!',
        });
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
});

    
    
    manualCloseModalButton.addEventListener('click', function() {
        editUserModal.hide();
    });

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const userId = row.getAttribute('data-user-id');
            const userName = row.cells[1].innerText;
            const userUsername = row.cells[2].innerText;
            const userEmail = row.cells[3].innerText;
            const userPhone = row.cells[4].innerText;

            document.getElementById('editUserId').value = userId;
            document.getElementById('editName').value = userName;
            document.getElementById('editUsername').value = userUsername;
            document.getElementById('editEmail').value = userEmail;
            document.getElementById('editPhone').value = userPhone;

            editUserModal.show();
        });
    });

    banButtons.forEach(button => {
        button.addEventListener('click', function(event) {
            event.preventDefault();
            let userID = this.getAttribute('data-user-id');
            let status = (this.closest('tr').querySelector('#status').innerText == "Active" ? 0 : 1);
            event.preventDefault();

            let confirmTitle, confirmText, confirmButtonText, successMessage;

            if (status === 0) {
                // User is not banned, ask to ban
                confirmTitle = 'Confirm Ban';
                confirmText = 'Are you sure you want to ban this user?';
                confirmButtonText = 'Yes, ban!';
                successMessage = 'User Banned';
            } else {
                // User is already banned, ask to unban
                confirmTitle = 'Confirm Unban';
                confirmText = 'Are you sure you want to unban this user?';
                confirmButtonText = 'Yes, unban!';
                successMessage = 'User Unbanned';
            }

            Swal.fire({
                title: confirmTitle,
                text: confirmText,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: confirmButtonText,
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // Perform the ban/unban action based on status
                    const actionURL = '/admin-ban-user/';
                    fetch(actionURL + userID, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token, 
                        },
                        body: JSON.stringify({
                            'userID' : userID,
                        }),
                    })
                    .then(response => {
                        if (response.ok && response.status === 200) {
                            return response.json();
                        } else {
                            throw new Error('Something went wrong');
                        }
                    })
                    .then(data => {
                        this.closest('tr').querySelector('#status').innerText = (status === 0 ? "Banned" : "Active");

                        Swal.fire(successMessage, `The user has been ${successMessage.toLowerCase()} successfully.`, 'success');
                    })
                    .catch(error => {
                        console.error('There has been a problem with your fetch operation:', error);
                    });
                }
            });
        });
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const userId = this.getAttribute('data-user-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete this user.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    // User confirmed, proceed with the delete action
                    fetch('/admin-delete-user/' + userId, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({
                            'userID': userId,
                        }),
                    })
                    .then(response => {
                        if (response.ok && response.status === 200) {
                            return response.json();
                        } else {
                            throw new Error('Something went wrong');
                        }
                    })
                    .then(data => {
                        // Handle success message or update the UI as needed
                        Swal.fire('User Deleted', 'The user has been deleted successfully.', 'success');
                        this.closest('tr').remove();
                    })
                    .catch(error => {
                        console.error('There has been a problem with your fetch operation:', error);
                    });
                }
            });
        });
    });
});
