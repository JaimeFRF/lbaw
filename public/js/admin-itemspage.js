document.addEventListener('DOMContentLoaded', function() {
    const editItemModalElement = document.getElementById('editItemModal'); // DOM element
    const editItemModal = new bootstrap.Modal(editItemModalElement); // Bootstrap modal object
    const editItemForm = document.getElementById('editItemForm');
    const addItemModalElement = document.getElementById('addItemModal'); // DOM element
    const addItemModal = new bootstrap.Modal(addItemModalElement); // Bootstrap modal object
    const addItemForm = document.getElementById('addItemForm');
    const manualCloseModalButton = document.getElementById('manualCloseModalButton');
    const categorySelect = document.getElementById('category');
    const subCategorySelect = document.getElementById('subCategory');
    const deleteItemButtons = document.querySelectorAll('.delete-item-btn');

    deleteItemButtons.forEach(button => {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            const itemId = this.getAttribute('data-item-id');

            Swal.fire({
                title: 'Are you sure?',
                text: 'You are about to remove all stock from this item.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, remove it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('/api/item/' + itemId, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            'itemId': itemId,
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
                        Swal.fire('Stock Removed', "The item's stock has been removed successfully.", 'success');
                        this.closest('tr').remove();
                    })
                    .catch(error => {
                        console.error('There has been a problem with your fetch operation:', error);
                    });
                }
            });
        });
    });

    
    addItemModalElement.addEventListener('hidden.bs.modal', function () {
        addItemForm.reset();
    });
    
    editItemModalElement.addEventListener('hidden.bs.modal', function () {
        editItemForm.reset();
    });
    
    manualCloseModalButton.addEventListener('click', function() {
        editItemModal.hide();
    });

    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const itemId = row.getAttribute('data-item-id'); // Ensure each row has a 'data-item-id' attribute
            const itemName = row.cells[1].innerText; // Adjust cell indices based on your table structure
            const itemCategory = row.cells[2].innerText;
            const itemSubCategory = row.cells[3].innerText;
            const itemSize = row.cells[4].innerText;
            const itemPrice = row.cells[5].innerText.replace('€', '').trim();
            const itemStock = row.cells[6].innerText;
            
            document.getElementById('editItemId').value = itemId;
            document.getElementById('editProductName').value = itemName;
            document.getElementById('editSubCategory').value = itemSubCategory;
            document.getElementById('editSize').value = itemSize;
            document.getElementById('editUnitPrice').value = itemPrice;
            document.getElementById('editStock').value = itemStock;
            
            // Set the correct category in the dropdown
            document.querySelectorAll('#editCategory option').forEach(option => {
                if (option.value.toLowerCase() === itemCategory.toLowerCase().replace(/\s+/g, '')) {
                    option.selected = true;
                } else {
                    option.selected = false;
                }
            });
            
            editItemModal.show();
        });
    });

    const updateItemButton = document.querySelector('.update-item-btn')

    updateItemButton.addEventListener('click', function (event) {
        
        event.preventDefault();

        const itemId = document.getElementById('editItemId').value;
        const formData = new FormData(editItemForm);
        formData.append('id_item', itemId);

        fetch(`/admin-update-item/${itemId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (response.ok) {
                return response.json();
            } else {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
        })
        .then(data => {
            const row = document.querySelector(`tr[data-item-id="${itemId}"]`);
            row.cells[1].innerText = data.updatedItemData.name;
            row.cells[2].innerText = data.updatedItemData.category;
            row.cells[3].innerText = data.updatedItemData.subCategory;
            row.cells[4].innerText = data.updatedItemData.size;
            row.cells[5].innerText = data.updatedItemData.price;
            row.cells[6].innerText = data.updatedItemData.stock;
            

            editItemModal.hide();
            Swal.fire({
                icon: 'success',
                title: 'Item Updated',
                text: 'The item has been updated successfully!',
            });
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
        
    });


    categorySelect.addEventListener('change', function() {
        const selectedCategory = this.value;
        subCategorySelect.innerHTML = '<option value="">Select a sub-category</option>'; // Reset sub-category dropdown
        subCategorySelect.disabled = true; // Keep disabled until data is loaded

        if (selectedCategory) {
            fetch(`/api/subcategories/${selectedCategory}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            }).then(response => response.json())
            .then(subCategories => {
                subCategories.forEach(subCategory => {
                    const option = document.createElement('option');
                    //option.value = subCategory.id; 
                    option.textContent = subCategory; 
                    subCategorySelect.appendChild(option);
                });

                if (subCategories.length > 0) {
                    subCategorySelect.disabled = false; 
                }
            })
            .catch(error => {
                console.error('Error fetching sub-categories:', error);
            });
        }
    });

    addItemForm.addEventListener('submit', function (event) {
        event.preventDefault();

        const formData = new FormData(addItemForm);

        fetch('/add-item', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
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
            addItemForm.reset();
            addItemModal.hide();
            Swal.fire('Item Added', `${data.item.name} has been added successfully to your shop.`, 'success')
            .then((result) => {
                if (result.isConfirmed) {
                    location.reload();
                }
            });
            //setTimeout(function() {location.reload();}, 2000);
        })
        .catch(error => {
            console.error('There was a problem with your fetch operation:', error);
        });
    });
    
});
