document.addEventListener('DOMContentLoaded', function() {
    const editItemModalElement = document.getElementById('editItemModal'); 
    const editItemModal = new bootstrap.Modal(editItemModalElement);
    const addItemModalElement = document.getElementById('addItemModal'); 
    const addItemModal = new bootstrap.Modal(addItemModalElement); 
    const editItemForm = document.getElementById('editItemForm');
    const addItemForm = document.getElementById('addItemForm');
    const categorySelect = document.getElementById('category');
    const categorySelectEdit = document.getElementById('categoryEdit');
    const subCategorySelect = document.getElementById('subCategory');
    const subCategorySelectEdit = document.getElementById('subCategoryEdit');
    const deleteItemButtons = document.querySelectorAll('.delete-item-btn');

    addItemModalElement.addEventListener('hidden.bs.modal', function () {
        addItemForm.reset();
    });
    
    editItemModalElement.addEventListener('hidden.bs.modal', function () {
        editItemForm.reset();
    });

    document.querySelectorAll('.edit-btn').forEach(function(button) {
        button.addEventListener('click', function() {
            const item = JSON.parse(this.getAttribute('data-item')); 
    
            editItemForm['editItemId'].value = item.id;
            editItemForm['editProductName'].value = item.name;
            editItemForm['editSize'].value = item.size;
            editItemForm['editUnitPrice'].value = item.price;
            editItemForm['editStock'].value = item.stock;
            categorySelectEdit.value = item.category;
            updateSubCategories(subCategorySelectEdit, item.category);

            subCategorySelectEdit.value = item.type;

            editItemModal.show();
        });
    });
    
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
                        const stockCell = this.closest('tr').children[6]; 
                        stockCell.textContent = '0'; 
                    })
                    .catch(error => {
                        console.error('There has been a problem with your fetch operation:', error);
                    });
                }
            });
        });
    });

    const updateItemButton = document.querySelector('.update-item-btn')

    updateItemButton.addEventListener('click', function (event) {
        event.preventDefault();

        const itemId = document.getElementById('editItemId').value;
        const formData = new FormData(editItemForm);
        formData.append('id_item', itemId);
        console.log(formData);

        fetch(`/admin-update-item/${itemId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
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

            Swal.fire({
                icon: 'error',
                title: 'Update Failed',
                text: 'There was a problem updating the item.',
            });
        });
        
    });

    categorySelectEdit.addEventListener('change', function() {
        updateSubCategories(subCategorySelectEdit, this.value);
    });

    function updateSubCategories(subCategorySelectElement, selectedCategory) {
        subCategorySelectElement.innerHTML = '<option value="">Select a sub-category</option>'; 

        if (selectedCategory) {
            fetch(`/api/subcategories/${selectedCategory}`, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
            })
            .then(response => response.json())
            .then(subCategories => {
                subCategories.forEach(subCategory => {
                    const option = document.createElement('option');
                    option.textContent = subCategory; 
                    option.value = subCategory;
                    subCategorySelectElement.appendChild(option);
                });

            })
            .catch(error => {
                console.error('Error fetching sub-categories:', error);
            });
        }
    }
    
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
        })
        .catch(error => {
            console.error('There was a problem with your fetch operation:', error);
        });
    });
    
});
