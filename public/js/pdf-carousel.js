document.addEventListener('DOMContentLoaded', function() {
    function initializeCarousel(carouselContainerElement) {
        const wrapper = carouselContainerElement.querySelector('.carousel-wrapper');
        const items = carouselContainerElement.querySelectorAll('.carousel-item');
        const prevButton = carouselContainerElement.querySelector('.carousel-button.prev');
        const nextButton = carouselContainerElement.querySelector('.carousel-button.next');
        const carouselInnerContainer = carouselContainerElement.querySelector('.carousel-container');

        if (!wrapper || items.length === 0 || !prevButton || !nextButton || !carouselInnerContainer) {
            // console.warn('Carousel elements not found in:', carouselContainerElement);
            return; // Skip if essential elements are missing
        }

        let currentPosition = 0;
        let itemWidth = items[0].offsetWidth + parseInt(getComputedStyle(items[0]).marginRight || '0', 10); // Include margin
        const totalItems = items.length;

        function getVisibleItems() {
            if (items.length === 0) return 0;
            itemWidth = items[0].offsetWidth + parseInt(getComputedStyle(items[0]).marginRight || '0', 10); // Recalculate itemWidth
            const containerWidth = carouselInnerContainer.offsetWidth;
            return Math.floor(containerWidth / itemWidth);
        }

        function updateCarousel() {
            wrapper.style.transform = `translateX(${currentPosition}px)`;
        }

        function moveNext() {
            const visibleItems = getVisibleItems();
            if (totalItems <= visibleItems) return; // No scroll if all items are visible

            const maxPosition = -(totalItems - visibleItems) * itemWidth;
            
            if (currentPosition > maxPosition) {
                currentPosition -= itemWidth;
            }
            
            if (currentPosition < maxPosition) {
                currentPosition = maxPosition;
            }
            updateCarousel();
        }

        function movePrev() {
            if (currentPosition < 0) {
                currentPosition += itemWidth;
            }
            
            if (currentPosition > 0) {
                currentPosition = 0;
            }
            updateCarousel();
        }

        prevButton.addEventListener('click', movePrev);
        nextButton.addEventListener('click', moveNext);

        // Add touch support
        let touchStartX = 0;
        wrapper.addEventListener('touchstart', e => {
            touchStartX = e.changedTouches[0].screenX;
        }, { passive: true });

        wrapper.addEventListener('touchend', e => {
            const touchEndX = e.changedTouches[0].screenX;
            if (touchStartX - touchEndX > 50) { // Swipe left
                moveNext();
            } else if (touchEndX - touchStartX > 50) { // Swipe right
                movePrev();
            }
        });

        // Add click listener for modals on mobile
        items.forEach(item => {
            item.addEventListener('click', function(event) {
                // Check if the click is on a button inside the item, if so, don't open modal
                if (event.target.closest('button') || event.target.closest('a')) {
                    return;
                }

                if (window.innerWidth < 768) { // Mobile breakpoint from CSS
                    const bookId = this.dataset.bookId;
                    if (bookId) {
                        window.dispatchEvent(new CustomEvent('open-modal', {
                            detail: { bookId: parseInt(bookId) }
                        }));
                    }
                }
            });
        });
        
        // Recalculate on resize
        window.addEventListener('resize', () => {
            currentPosition = 0; // Reset position on resize
            updateCarousel();
        });
    }

    // Initialize all carousels on the page
    const allCarousels = document.querySelectorAll('.newest-books-container'); // Assuming this is the main container for each carousel
    allCarousels.forEach(carousel => {
        initializeCarousel(carousel);
    });

    // Note: The toggleFavorite function was present in the original script.
    // It seems unrelated to the current carousel's "Read Later" (bookmark) functionality.
    // I'm keeping it here commented out in case it's used elsewhere or was for a different feature.
    /*
    function toggleFavorite(bookId) {
        const button = event.currentTarget;
        fetch(`/favorites/${bookId}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'added') {
                button.classList.add('active');
                button.querySelector('i').classList.remove('bx-heart');
                button.querySelector('i').classList.add('bxs-heart');
            } else {
                button.classList.remove('active');
                button.querySelector('i').classList.remove('bxs-heart');
                button.querySelector('i').classList.add('bx-heart');
            }
        })
        .catch(error => console.error('Error:', error));
    }
    */
});
