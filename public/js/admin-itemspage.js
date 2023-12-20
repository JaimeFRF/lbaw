document.addEventListener('DOMContentLoaded', function() {
    const editItemModalElement = document.getElementById('editItemModal'); 
    const editItemModal = new bootstrap.Modal(editItemModalElement);
    const addItemModalElement = document.getElementById('addItemModal'); 
    const addItemModal = new bootstrap.Modal(addItemModalElement); 
    const editItemForm = document.getElementById('editItemForm');
    const addItemForm = document.getElementById('addItemForm');
    const manualCloseModalButton = document.getElementById('manualCloseModalButton');
    const categorySelect = document.getElementById('category');
    const categorySelectEdit = document.getElementById('Editcategory');
    const subCategorySelect = document.getElementById('subCategory');
    const subCategorySelectEdit = document.getElementById('subCategoryEdit');
    const deleteItemButtons = document.querySelectorAll('.delete-item-btn');


    addItemModalElement.addEventListener('hidden.bs.modal', function () {
        addItemForm.reset();
    });
    
    editItemModalElement.addEventListener('hidden.bs.modal', function () {
        editItemForm.reset();
    });

    // document.querySelectorAll('.edit-btn').forEach(function(button) {
    //     button.addEventListener('click', function() {
    //         var item = JSON.parse(this.getAttribute('data-item')); 
    
    //         document.getElementById('editItemId').value = item.id;
    //         document.getElementById('editProductName').value = item.name;
    //         document.getElementById('editSize').value = item.size;
    //         document.getElementById('editUnitPrice').value = item.price;
    //         document.getElementById('editStock').value = item.stock;
    //     });
    // });
    
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



    document.querySelectorAll('.edit-btn').forEach(button => {
        button.addEventListener('click', function () {
            const row = this.closest('tr');
            const itemId = row.getAttribute('data-item-id'); 
            const itemName = row.cells[1].innerText; 
            const itemCategory = row.cells[2].innerText;
            const itemSubCategory = row.cells[3].innerText;
            const itemSize = row.cells[4].innerText;
            const itemPrice = row.cells[5].innerText.replace('€', '').trim();
            const itemStock = row.cells[6].innerText;
            
            document.getElementById('editItemId').value = itemId;
            document.getElementById('editProductName').value = itemName;
            document.getElementById('Editcategory').value = itemCategory;
            // document.getElementById('subCategoryEdit').value = itemSubCategory;
            document.getElementById('editSize').value = itemSize;
            document.getElementById('editUnitPrice').value = itemPrice;
            document.getElementById('editStock').value = itemStock;
            
            document.querySelectorAll('#editCategory option').forEach(option => {

                if (option.text.trim().toLowerCase() === itemCategory.trim().toLowerCase()) {
                    option.selected = true;
                } else {
                    option.selected = false;
                }
            });
            const selectedCategory = itemCategory;
            subCategorySelectEdit.innerHTML = '<option value="">Select a sub-category</option>'; 
            subCategorySelectEdit.disabled = true; 

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
                        option.textContent = subCategory; 
                        subCategorySelectEdit.appendChild(option);
                        if (subCategory == itemSubCategory) option.selected = true;
                    });

                    if (subCategories.length > 0) {
                        subCategorySelectEdit.disabled = false; 
                    }
                })
                .catch(error => {
                    console.error('Error fetching sub-categories:', error);
                });
            }

            const container = document.getElementById('itemImagesContainer');
            container.innerHTML = ''; 
            fetch(`/api/get/item-picture/${itemId}` , {
                method: 'GET',
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
            }).then(images => {
                console.log(images);
                images.forEach(image => {
                const imgElement = document.createElement('img');
                imgElement.src = image.filepath; 
                imgElement.classList.add('img-thumbnail', 'mr-2');

                const deleteButton = document.createElement('button');
                deleteButton.innerText = 'Delete';
                deleteButton.classList.add('btn', 'btn-danger', 'btn-sm');
                
                
                const imageDiv = document.createElement('div');
                imageDiv.classList.add('image-container','d-flex', 'flex-column', 'align-items-center', 'mr-3', 'mb-3');
                imageDiv.appendChild(imgElement);
                deleteButton.addEventListener('click', function(event) {
                    event.preventDefault();
                    event.stopPropagation();
                    fetch('/delete-item-image', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        },
                        body: JSON.stringify({
                            'imageId': image.id,
                        }),
                    }).then(response => {
                        if (response.status == 200) {
                            return response.json();
                        }
                        else {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                    }).then(
                        data => {
                            console.log(data);
                            const imageDiv = this.closest('.image-container');
                            console.log(imageDiv);
                            imageDiv.remove();
                        }
                    ).catch(error => {
                        console.error('Error:', error);
                    });
                });
            
                imageDiv.appendChild(deleteButton);
                container.appendChild(imageDiv);
            });
            editItemModal.show();
            })
    });
    });


    const updateItemButton = document.querySelector('.update-item-btn');

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
            if(data.updatedItemData.category != "") {row.cells[2].innerText = data.updatedItemData.category;}
            if(data.updatedItemData.subCategory != "") {row.cells[3].innerText = data.updatedItemData.subCategory;}
            row.cells[4].innerText = data.updatedItemData.size;
            row.cells[5].innerText = data.updatedItemData.price;
            row.cells[6].innerText = data.updatedItemData.stock;
            

            Swal.fire({
                icon: 'success',
                title: 'Item Updated',
                text: 'The item has been updated successfully!',
            });
            editItemModal.hide();
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
        
    });

    

    categorySelect.addEventListener('change', function (){
        const selectedCategory = this.value;
        subCategorySelect.innerHTML = '<option value="">Select a sub-category</option>'; 
        console.log("hey");
        // subCategorySelect.disabled = true; 

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

    categorySelectEdit.addEventListener('change', function() {
        const selectedCategory = this.value;
        console.log(selectedCategory);
        subCategorySelectEdit.innerHTML = '<option value="">Select a sub-category</option>'; 
        subCategorySelectEdit.disabled = true; 

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
                    option.textContent = subCategory; 
                    subCategorySelectEdit.appendChild(option);
                });

                if (subCategories.length > 0) {
                    subCategorySelectEdit.disabled = false; 
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
        })
        .catch(error => {
            console.error('There was a problem with your fetch operation:', error);
        });
    });
    
});
