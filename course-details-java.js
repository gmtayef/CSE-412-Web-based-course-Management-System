function showContent(contentType) {
    const contentElements = document.querySelectorAll('.content');
    contentElements.forEach(element => {
        element.style.display = 'none';
    });

    const selectedContent = document.getElementById(`${contentType}-content`);
    if (selectedContent) {
        selectedContent.style.display = 'block';
    }
}

function showSuccessMessage() {
    const successMessage = document.getElementById("success-message");
    successMessage.style.display = "block";

    setTimeout(function() {
        successMessage.style.display = "none";
    }, 3000); // Hide after 3 seconds
}

// Initial display
showContent('overview');







