document.addEventListener('DOMContentLoaded', function() {
    function initializeCarousel(carouselContainerElement) {
        const wrapper = carouselContainerElement.querySelector('.carousel-wrapper');
        const items = carouselContainerElement.querySelectorAll('.carousel-item');
        const prevButton = carouselContainerElement.querySelector('.carousel-button.prev');
        const nextButton = carouselContainerElement.querySelector('.carousel-button.next');
        const carouselInnerContainer = carouselContainerElement.querySelector('.carousel-container');

        if (!wrapper || items.length === 0 || !prevButton || !nextButton || !carouselInnerContainer) {
            return;
        }

        let currentPosition = 0;
        const totalItems = items.length;
        let itemWidthWithGap = 0;

        function calculateDimensions() {
            if (items.length === 0) return;
            const itemStyle = getComputedStyle(items[0]);
            const itemOffsetWidth = items[0].offsetWidth;
            const gap = parseInt(getComputedStyle(wrapper).gap || '0', 10);
            itemWidthWithGap = itemOffsetWidth + gap;
        }

        // Initial calculation
        calculateDimensions();

        function getVisibleItemsCount() {
            if (items.length === 0 || itemWidthWithGap === 0) return 0;
            const containerWidth = carouselInnerContainer.offsetWidth;
            const gap = parseInt(getComputedStyle(wrapper).gap || '0', 10);
            return Math.floor((containerWidth + gap) / itemWidthWithGap);
        }

        function updateCarousel() {
            const visibleItems = getVisibleItemsCount();
            if (totalItems <= visibleItems) { 
                currentPosition = 0;
            } else {
                if (itemWidthWithGap > 0) {
                    const maxScroll = (totalItems - visibleItems) * itemWidthWithGap;
                    const maxPositionValue = -maxScroll;
                    if (currentPosition < maxPositionValue) currentPosition = maxPositionValue;
                }
                if (currentPosition > 0) currentPosition = 0;
            }
            wrapper.style.transform = `translateX(${currentPosition}px)`;
        }

        function moveNext() {
            calculateDimensions();
            const visibleItems = getVisibleItemsCount();

            if (totalItems <= visibleItems || itemWidthWithGap === 0) {
                currentPosition = 0;
                updateCarousel();
                return;
            }

            const itemsToScroll = 2;
            const scrollUnit = itemWidthWithGap;
            const maxScrollablePosition = -((totalItems - visibleItems) * scrollUnit);
            
            const itemsScrolled = Math.round(Math.abs(currentPosition / scrollUnit));
            const remainingItemsToRight = (totalItems - visibleItems) - itemsScrolled;

            let actualItemsToScroll = itemsToScroll;
            if (remainingItemsToRight < itemsToScroll && remainingItemsToRight > 0) {
                actualItemsToScroll = remainingItemsToRight;
            } else if (remainingItemsToRight <= 0) {
                currentPosition = maxScrollablePosition;
                updateCarousel();
                return;
            }
            
            currentPosition -= actualItemsToScroll * scrollUnit;

            if (currentPosition < maxScrollablePosition) {
                currentPosition = maxScrollablePosition;
            }
            updateCarousel();
        }

        function movePrev() {
            calculateDimensions();
            const visibleItems = getVisibleItemsCount();

            if (totalItems <= visibleItems || itemWidthWithGap === 0) {
                currentPosition = 0;
                updateCarousel();
                return;
            }

            const itemsToScroll = 2;
            const scrollUnit = itemWidthWithGap;
            
            const itemsScrolledToLeft = Math.round(Math.abs(currentPosition / scrollUnit));
            let actualItemsToScroll = itemsToScroll;
            if (itemsScrolledToLeft < itemsToScroll && itemsScrolledToLeft > 0) {
                actualItemsToScroll = itemsScrolledToLeft;
            } else if (itemsScrolledToLeft <= 0) {
                currentPosition = 0;
                updateCarousel();
                return;
            }

            currentPosition += actualItemsToScroll * scrollUnit;

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
            if (touchStartX - touchEndX > 50) {
                moveNext();
            } else if (touchEndX - touchStartX > 50) {
                movePrev();
            }
        });

        items.forEach(item => {
            item.addEventListener('click', function(event) {
                if (event.target.closest('button') || event.target.closest('a')) {
                    return;
                }

                if (window.innerWidth < 768) {
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
            const firstVisibleItemIndexBeforeResize = (itemWidthWithGap > 0) ? Math.round(Math.abs(currentPosition) / itemWidthWithGap) : 0;
            
            calculateDimensions();
            
            const visibleItems = getVisibleItemsCount();
            if (totalItems <= visibleItems || itemWidthWithGap === 0) {
                currentPosition = 0;
            } else {
                currentPosition = -(firstVisibleItemIndexBeforeResize * itemWidthWithGap);
                const maxScroll = (totalItems - visibleItems) * itemWidthWithGap;
                const maxPositionValue = -maxScroll;

                if (currentPosition < maxPositionValue) {
                    currentPosition = maxPositionValue;
                }
                if (currentPosition > 0) { 
                    currentPosition = 0;
                }
            }
            updateCarousel();
        });
    }

    const allCarousels = document.querySelectorAll('.newest-books-container');
    allCarousels.forEach(carousel => {
        initializeCarousel(carousel);
    });

});
