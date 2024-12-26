document.addEventListener('DOMContentLoaded', function() {
    const wrapper = document.querySelector('.carousel-wrapper');
    const items = document.querySelectorAll('.carousel-item');
    const prevButton = document.querySelector('.carousel-button.prev');
    const nextButton = document.querySelector('.carousel-button.next');
    
    let currentPosition = 0;
    const itemWidth = items[0].offsetWidth;
    const totalItems = items.length;
    
    function getVisibleItems() {
        const containerWidth = document.querySelector('.carousel-container').offsetWidth;
        return Math.floor(containerWidth / itemWidth);
    }
    
    function updateCarousel() {
        wrapper.style.transform = `translateX(${currentPosition}px)`;
    }
    
    function moveNext() {
        const visibleItems = getVisibleItems();
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
    
    // Add touch support for mobile devices
    let touchStartX = 0;
    let touchEndX = 0;
    
    wrapper.addEventListener('touchstart', e => {
        touchStartX = e.changedTouches[0].screenX;
    });
    
    wrapper.addEventListener('touchend', e => {
        touchEndX = e.changedTouches[0].screenX;
        if (touchStartX - touchEndX > 50) {
            moveNext();
        } else if (touchEndX - touchStartX > 50) {
            movePrev();
        }
    });
    
    // Handle favorites
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
});
