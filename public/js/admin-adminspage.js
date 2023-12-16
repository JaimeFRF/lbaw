document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('[id="delete"]');
    let token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const editButtons = document.querySelectorAll('.edit-btn'); 
    const editAdminModal = new bootstrap.Modal(document.getElementById('editAdminModal'));
    const manualCloseModalButton = document.getElementById('manualCloseModalButton');
    const editAdminForm = document.getElementById('editAdminForm');
    const updateAdminButton = document.querySelector('.update-admin-btn');
    const addAdminForm = document.getElementById('addAdminForm');
    const addAdminModal = new bootstrap.Modal(document.getElementById('addAdminModal'));

    addAdminForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(addAdminForm);

        fetch('/admin-add-admin', {
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
            addAdminForm.reset();
            addAdminModal.hide();
            Swal.fire('Admin Added', `Admin ${data.username} has been added successfully.`, 'success');
            location.reload();
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
    });

    updateAdminButton.addEventListener('click', function(event) {
        event.preventDefault();

        const adminId = document.getElementById('editAdminId').value;
        const formData = new FormData(editAdminForm);
        formData.append('admin_id', adminId);

        fetch(`/admin-update-admin/${adminId}`, {
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
            const row = document.querySelector(`tr[data-user-id="${adminId}"]`);
            console.log(data);
            row.cells[1].innerText = data.updatedAdminData.username;  
            row.cells[2].innerText = data.updatedAdminData.email;  
            row.cells[3].innerText = data.updatedAdminData.phone; 

            editAdminModal.hide();
            Swal.fire({
                icon: 'success',
                title: 'Admin Updated',
                text: 'The admin has been updated successfully!',
            });
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
    });

    manualCloseModalButton.addEventListener('click', function() {
        editAdminModal.hide();
    });

    editButtons.forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const adminId = row.getAttribute('data-user-id');
            const adminName = row.cells[1].innerText;
            const adminUsername = row.cells[2].innerText;
            const adminEmail = row.cells[3].innerText;
            const adminPhone = row.cells[4].innerText;

            document.getElementById('editAdminId').value = adminId;
            document.getElementById('editAdminName').value = adminName;
            document.getElementById('editAdminUsername').value = adminUsername;
            document.getElementById('editAdminEmail').value = adminEmail;
            document.getElementById('editAdminPhone').value = adminPhone;

            editAdminModal.show();
        });
    });

    deleteButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const adminId = this.getAttribute('data-user-id');
            
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete this admin.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/admin-delete-admin/' + adminId, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({
                            'adminId': adminId,
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
                        Swal.fire('Admin Deleted', 'The admin has been deleted successfully.', 'success');
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
