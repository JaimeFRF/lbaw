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
                console.log(subCategories);
                subCategories.forEach(subCategory => {
                    console.log(subCategory);
                    const option = document.createElement('option');
                    //option.value = subCategory.id; 
                    option.textContent = subCategory; 
                    subCategorySelect.appendChild(option);
                    console.log(subCategorySelect);
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

        fetch('/admin-add-item', {
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
            Swal.fire('Item Added', `Item ${data.name} has been added successfully.`, 'success');
            location.reload();
        })
        .catch(error => {
            console.error('There was a problem with your fetch operation:', error);
        });
    });
    
});
