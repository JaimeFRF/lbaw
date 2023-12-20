window.onload = function() {
    updateNavbar();
};

function updateNavbar() {
    
    fetch('/api/cart/count', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
        
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        document.getElementById("ItemCartNumber").innerText = "(" + data.count +")";
    })
    .catch(error => {
        console.error('There has been a problem with your fetch operation:', error);
    });
}


function removeNotification(id) {
    console.log(id);
    fetch('/notifications/delete/' + id, {
        method: 'DELETE',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
      }
    })
    .then((response) => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        var element = document.querySelector('#notification-' + id);
        if (element) {
            element.remove();
            const notificationsCountElement = document.getElementById('notificationsCount');
            fetch('/notifications/count')
            .then(response => response.json())
            .then(data => {
              notificationsCountElement.innerText = `(${data.notificationsCount})`;
            })
            .catch(error => console.error('Error:', error));
        }
    })
    .catch((error) => {
        console.error('There has been a problem with your fetch operation:', error);
    });
}

function toggleDropdown() {
    var dropdownMenu = document.getElementById('dropdownMenu');
    if (dropdownMenu.style.display === "none") {
      dropdownMenu.style.display = "block";
    } else {
      dropdownMenu.style.display = "none";
    }
}

document.addEventListener('DOMContentLoaded', function() {
    var dropdownItems = document.querySelectorAll('.notifi-item');
    dropdownItems.forEach(function(dropdownItem) {
      dropdownItem.addEventListener('click', function(e) {
        e.stopPropagation();
      });
    });
  });