document.getElementById('imageInput').addEventListener('change', function(event) {
    var output = document.getElementById('imagePreview');
    output.style.display = 'block'; 
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) 
    }
});

function confirmDelete() {
    var confirmation = confirm("Are you sure you want to delete your profile?");
    if (confirmation) {
      return true;
    } else {
      return false;
    }
}
