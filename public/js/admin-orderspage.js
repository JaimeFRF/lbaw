document.addEventListener('DOMContentLoaded', function() {
    const deleteOrderButtons = document.querySelectorAll('[id="delete"]');
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const updateOrderButton = document.getElementById('updateOrderButton');
    const detailedOrderModalElement = document.getElementById('detailedOrderModal');
    const detailedOrderModal = new bootstrap.Modal(detailedOrderModalElement);
    const detailedOrderForm = document.getElementById('detailedOrderForm');
    const orderRows = document.querySelectorAll('.order-row:not(#delete)');
    // const editOrderButtons = document.querySelectorAll('.edit-btn'); 
    // const editOrderForm = document.getElementById('editOrderForm');
    // const editOrderModalElement = document.getElementById('editOrderModal'); 
    // const editOrderModal = new bootstrap.Modal(editOrderModalElement);
    // const addOrderForm = document.getElementById('addOrderForm');
    // const addOrderModalElement = document.getElementById('addOrderModal'); 
    // const addOrderModal = new bootstrap.Modal(addOrderModalElement);

    orderRows.forEach(row => {
        row.addEventListener('click', function() {
            var isButton = event.target.tagName.toLowerCase() === 'button';
            var isSpan = event.target.tagName.toLowerCase() === 'span';
            if (!isButton && !isSpan) {
                const orderId = this.getAttribute('data-order-id');
                const customerName = this.cells[1].innerText;
                const orderAmountWithCurrency = this.cells[2].innerText; // e.g., "123€"
                const orderAmount = orderAmountWithCurrency.replace(/[^\d.-]/g, '');
                const deliveryDate = this.cells[4].innerText;
                const orderStatus = this.cells[5].innerText;
                fetch(`/admin-get-order-address-info/${orderId}`, {
                    method: 'GET',
                    headers: {'X-CSRF-TOKEN': token}
                }).then(response => {
                    if (response.ok && response.status === 200) {
                        return response.json();
                } else {
                    throw new Error('Something went wrong');
                }
                })
                .then(data => {
                        const orderAddress = data.location.address;
                        const orderCity = data.location.city;
                        const orderCountry = data.location.country;
                        const orderPostalCode = data.location.postal_code;
                        document.getElementById('detailedOrderId').value = orderId;
                        document.getElementById('detailedCustomerName').value = customerName;
                        document.getElementById('detailedOrderAmount').value = orderAmount;
                        document.getElementById('detailedOrderDeliveryDate').value = deliveryDate;
                        document.getElementById('detailedOrderStatus').value = orderStatus;
                        document.getElementById('detailedOrderAddress').value = orderAddress;
                        document.getElementById('detailedOrderCity').value = orderCity;
                        document.getElementById('detailedOrderCountry').value = orderCountry;
                        document.getElementById('detailedOrderPostalCode').value = orderPostalCode;
                        detailedOrderModal.show();
                })
                .catch(error => console.error('There has been a problem with your fetch operation:', error));
            }
        });
    });
    
    detailedOrderForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const formData = new FormData(detailedOrderForm);

        fetch(`/admin-update-order`, {
            method: 'POST',
            body: formData,
            headers: {'X-CSRF-TOKEN': token}
        })
        .then(data => {
            if (data.status == 200) {
            Swal.fire('Order updated', `Order ${data.id} has been updated successfully.`, 'success');
            detailedOrderModal.hide();
        } else {
            console.log('Update failed: ' + data.message);
        }})
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
                    fetch('/purchase/delete/' + orderId, {
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


