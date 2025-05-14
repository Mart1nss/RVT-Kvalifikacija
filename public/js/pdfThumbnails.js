// Book Thumbnails for Carousel WELCOME PAGE
document.addEventListener('DOMContentLoaded', function() {
    // Adjust carousel animation speed based on visible items
    const carouselTrack = document.querySelector('.carousel-track');
    if (carouselTrack) {
        // Get all items to ensure we have enough for a smooth loop
        const items = document.querySelectorAll('.pdf-item');
        const totalItems = items.length;
        
        if (totalItems > 0) {
            // The speed should be proportional to the number of items
            // More items = slower animation for smooth scrolling
            const speed = Math.max(30, totalItems * 1.5); // Adjust speed based on item count
            carouselTrack.style.animationDuration = speed + 's';
            
            // If we have too few items, this may indicate filtering removed many books
            // Add a class to ensure the animation still works properly
            if (totalItems < 10) {
                carouselTrack.classList.add('few-items');
            }
        } else {
            // No items to display - hide the carousel
            const carouselSection = document.querySelector('.library-preview');
            if (carouselSection) {
                carouselSection.style.display = 'none';
            }
        }
    }
});