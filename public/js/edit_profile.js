document.getElementById('imageInput').addEventListener('change', function(event) {
    var output = document.getElementById('imagePreview');
    output.style.display = 'block'; // Show the image element
    output.src = URL.createObjectURL(event.target.files[0]);
    output.onload = function() {
        URL.revokeObjectURL(output.src) // Free up memory
    }
});
