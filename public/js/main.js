// Add any global JavaScript functionality here

// Example: Add a confirmation dialog to all delete buttons
document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.btn-danger');
    deleteButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
});

// Example: Add a simple loading indicator
function showLoading() {
    const loadingDiv = document.createElement('div');
    loadingDiv.id = 'loading';
    loadingDiv.textContent = 'Loading...';
    loadingDiv.style.position = 'fixed';
    loadingDiv.style.top = '50%';
    loadingDiv.style.left = '50%';
    loadingDiv.style.transform = 'translate(-50%, -50%)';
    loadingDiv.style.background = 'rgba(0, 0, 0, 0.5)';
    loadingDiv.style.color = 'white';
    loadingDiv.style.padding = '1rem';
    loadingDiv.style.borderRadius = '5px';
    document.body.appendChild(loadingDiv);
}

function hideLoading() {
    const loadingDiv = document.getElementById('loading');
    if (loadingDiv) {
        loadingDiv.remove();
    }
}

// Use these functions when making AJAX requests
// Example:
// showLoading();
// fetch('/some-url')
//     .then(response => response.json())
//     .then(data => {
//         // Handle the data
//     })
//     .catch(error => {
//         console.error('Error:', error);
//     })
//     .finally(() => {
//         hideLoading();
//     });