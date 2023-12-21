document.addEventListener('DOMContentLoaded', function() {
    var currentSessionCategory = window.currentSessionCategory;
    var categorySelect = document.getElementById('category');
    var subcategorySelect = document.getElementById('subcategorySelect');
    var subcategoryDiv = document.getElementById('subcategoryDiv');


    function getCheckedValues() {
        var checkboxes = document.querySelectorAll('#subcategoryDiv input[type=checkbox]:checked');
        var values = [];
        for (var i = 0; i < checkboxes.length; i++) {
            values.push(checkboxes[i].value);
        }
        document.getElementById('shoeSizes').value = values.join(','); 
    }

    document.getElementById('filter').addEventListener('submit', getCheckedValues);


    function populateSubcategory(category) {

        if (category === 'sneakers') {
            subcategorySelect.style.display = 'none';

            subcategoryDiv.innerHTML = '';

            for (var i = 34; i <= 47; i++) {
                var checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.id = 'size' + i;
                checkbox.value = i;
                checkbox.className = 'button-checkbox';

                var label = document.createElement('label');
                label.htmlFor = 'size' + i;
                label.className = 'button-label';
                label.appendChild(document.createTextNode(i));

                var div = document.createElement('div');
                div.style.flex = '0 0 auto'; 

                div.appendChild(checkbox);
                div.appendChild(label);

                subcategoryDiv.appendChild(div);
            }
            subcategoryDiv.style.display = 'flex';
        } 
        else {                
            subcategoryDiv.style.display = 'none';
            subcategorySelect.innerHTML = '';

            var defaultOption = document.createElement('option');
            defaultOption.value = 'None';
            defaultOption.text = 'Choose a subcategory...';
            subcategorySelect.appendChild(defaultOption);

            if (category !== 'all' && category !== 'None'  && category !== 'sneakers') {
                fetch('/api/subcategories/' + category)
                    .then(response => response.json())
                    .then(data => {
                        data.forEach(function(subcategory) {
                            var option = document.createElement('option');
                            option.value = subcategory;
                            option.text = subcategory;
                            subcategorySelect.appendChild(option);
                        });
                        subcategorySelect.style.display = 'block';
                    });
            } else {
                subcategorySelect.style.display = 'none';
            }
        }
    }

    if (currentSessionCategory && currentSessionCategory !== 'all' && currentSessionCategory !== 'None') {
        populateSubcategory(currentSessionCategory);
    }

    categorySelect.addEventListener('change', function() {
        populateSubcategory(this.value);
    });
});