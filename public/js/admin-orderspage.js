document.addEventListener('DOMContentLoaded', function() {
    const deleteOrderButtons = document.querySelectorAll('[id="delete"]');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const editOrderButtons = document.querySelectorAll('.edit-btn'); 
    const updateOrderButton = document.querySelector('.update-order-btn');
    const editOrderForm = document.getElementById('editOrderForm');
    const editOrderModalElement = document.getElementById('editOrderModal'); 
    const editOrderModal = new bootstrap.Modal(editOrderModalElement);
    const addOrderForm = document.getElementById('addOrderForm');
    const addOrderModalElement = document.getElementById('addOrderModal'); 
    const addOrderModal = new bootstrap.Modal(addOrderModalElement);

    addOrderModalElement.addEventListener('hidden.bs.modal', function () {
        addOrderForm.reset();
    });
    
    editOrderModalElement.addEventListener('hidden.bs.modal', function () {
        editOrderForm.reset();
    }); 

    addOrderForm.addEventListener('submit', function (event) {
        event.preventDefault();
        const formData = new FormData(addOrderForm);
        fetch('/admin-add-order', {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': token},
        })
        .then(response => response.ok && response.status === 200 ? response.json() : Promise.reject('Something went wrong'))
        .then(data => {
            addOrderForm.reset();
            addOrderModal.hide();
            Swal.fire('Order Added', `Order has been added successfully.`, 'success');
            location.reload();
        })
        .catch(error => console.error('There has been a problem with your fetch operation:', error));
    });

    document.querySelector('.table').addEventListener('click', function(event) {
        if (event.target && event.target.matches('.edit-btn')) {
            const button = event.target;
            const row = button.closest('tr');
            const orderId = row.getAttribute('data-order-id');
            const customerName = row.cells[1].innerText; 
            const orderAmount = row.cells[2].innerText; 

            document.getElementById('editOrderId').value = orderId;
            document.getElementById('editCustomerName').value = customerName;
            document.getElementById('editOrderAmount').value = orderAmount;

            editOrderModal.show();
        }
    });

    updateOrderButton.addEventListener('click', function(event) {
        event.preventDefault();
        const orderId = document.getElementById('editOrderId').value;
        const formData = new FormData(editOrderForm);
        formData.append('order_id', orderId);
        fetch(`/admin-update-order/${orderId}`, {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': token}
        })
        .then(response => response.ok && response.status === 200 ? response.json() : Promise.reject('Something went wrong'))
        .then(data => {
            const row = document.querySelector(`tr[data-order-id="${orderId}"]`);
            row.cells[1].innerText = data.updatedOrderData.customerName;
            row.cells[2].innerText = data.updatedOrderData.price;
            row.cells[3].innerText = data.updatedOrderData.purchaseDate;
            editOrderModal.hide();
            Swal.fire('Order Updated', 'The order has been updated successfully!', 'success');
        })
        .catch(error => console.error('There has been a problem with your fetch operation:', error));
    });


    deleteOrderButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const orderId = this.getAttribute('data-order-id');
            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to delete this order.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'No, keep it',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/admin-delete-order/' + orderId, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                        },
                        body: JSON.stringify({'orderId': orderId}),
                    })
                    .then(response => response.ok && response.status === 200 ? response.json() : Promise.reject('Something went wrong'))
                    .then(data => {
                        Swal.fire('Order Deleted', 'The order has been deleted successfully.', 'success');
                        this.closest('tr').remove();
                    })
                    .catch(error => console.error('There has been a problem with your fetch operation:', error));
                }
            });
        });
    });
});
