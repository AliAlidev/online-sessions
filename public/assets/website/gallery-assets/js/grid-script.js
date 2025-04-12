// Function to handle the grid item layout adjustments
function handleGridItemLayout() {
    const gridItems = document.querySelectorAll(".grid-item");

    gridItems.forEach(item => {
        const img = item.querySelector('img');

        // Wait for the image to load
        img.onload = () => {
            const rowHeight = 10; // Match the grid-auto-rows value in CSS
            const imageHeight = img.naturalHeight;
            const imageWidth = img.naturalWidth;
            const screenWidth = window.innerWidth;

            // Check if screen width is less than 600px
            if (screenWidth < 560) {
                // On small screens, portrait images span 1.5 rows (15), landscape span 1 row (10)
                if (imageHeight > imageWidth) {
                    item.style.gridRowEnd = `span 16`; // Portrait images on small screens
                } else {
                    item.style.gridRowEnd = `span 8`; // Landscape images on small screens
                }
            } else {
                // On larger screens, portrait images span 2 rows (20), landscape span 1 row (10)
                if (imageHeight > imageWidth) {
                    item.style.gridRowEnd = `span 20`; // Portrait images on large screens
                } else {
                    item.style.gridRowEnd = `span 10`; // Landscape images on large screens
                }
            }
        };

        // Ensure to account for cached images (sometimes onload won't fire for cached images)
        if (img.complete) {
            img.onload();
        }
    });
}

// Wait for the DOM to be fully loaded before running the observer
document.addEventListener("DOMContentLoaded", function() {
    // Use MutationObserver to detect when .grid-item elements are added to the DOM
    const observer = new MutationObserver((mutationsList) => {
        mutationsList.forEach(mutation => {
            if (mutation.addedNodes.length > 0) {
                mutation.addedNodes.forEach(node => {
                    if (node.classList && node.classList.contains('grid-item')) {
                        // Run the function when a .grid-item is added
                        handleGridItemLayout();
                    }
                });
            }
        });
    });

    // Observe the entire document for added nodes
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});
