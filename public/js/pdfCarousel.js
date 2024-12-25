// Carousel functionality
const track = document.querySelector('.carousel-track');
const slides = Array.from(track.children);
const nextButton = document.querySelector('.carousel-next');
const prevButton = document.querySelector('.carousel-prev');
const slideWidth = slides[0].getBoundingClientRect().width;
const gap = parseInt(getComputedStyle(track).gap); // Get the gap between slides
const slidesPerPage = 2; // Number of slides to move at a time

// Helper function to set initial slide positions
const setSlidePosition = (slide, index) => {
    slide.style.left = (slideWidth + gap) * index + 'px';
};
slides.forEach(setSlidePosition);

// Function to move to a specific slide
const moveToSlide = (track, currentSlide, targetSlide) => {
    const currentSlideIndex = slides.indexOf(currentSlide);
    const targetSlideIndex = slides.indexOf(targetSlide);
    const moveAmount = (slideWidth + gap) * targetSlideIndex; // Ensure it moves by the total width of the slides including gap
    track.style.transform = 'translateX(-' + moveAmount + 'px)';
    currentSlide.classList.remove('current-slide');
    targetSlide.classList.add('current-slide');
};

// Get the next slide, handling wraparound
const getNextSlide = (currentSlide) => {
    const currentSlideIndex = slides.indexOf(currentSlide);
    let nextSlideIndex = (currentSlideIndex + slidesPerPage) % slides.length;
    return slides[nextSlideIndex];
};

// Get the previous slide, handling wraparound
const getPrevSlide = (currentSlide) => {
    const currentSlideIndex = slides.indexOf(currentSlide);
    let prevSlideIndex = (currentSlideIndex - slidesPerPage + slides.length) % slides.length;
    return slides[prevSlideIndex];
};

// Update the currentSlide variable within the event listeners
nextButton.addEventListener('click', () => {
    const currentSlide = track.querySelector('.current-slide');
    const nextSlide = getNextSlide(currentSlide);
    moveToSlide(track, currentSlide, nextSlide);
});

prevButton.addEventListener('click', () => {
    const currentSlide = track.querySelector('.current-slide');
    const prevSlide = getPrevSlide(currentSlide);
    moveToSlide(track, currentSlide, prevSlide);
});

// Initially mark the first slide as current
slides[0].classList.add('current-slide');


// Dragging functionality
let startX, currentX, initialPosition, isDragging = false;

track.addEventListener('touchstart', (e) => {
    startX = e.touches[0].clientX;
    initialPosition = track.style.transform ? parseInt(track.style.transform.split('(')[1].split('px')[0]) : 0;
    isDragging = true;
    track.style.transition = 'none';
});

track.addEventListener('touchmove', (e) => {
    if (!isDragging) return;
    currentX = e.touches[0].clientX;
    const moveX = currentX - startX;
    track.style.transform = `translateX(${initialPosition + moveX}px)`;
});

track.addEventListener('touchend', (e) => {
    if (!isDragging) return;
    isDragging = false;
    track.style.transition = 'transform 0.3s ease-in';
    const moveX = currentX - startX;

    if (moveX < -50) {
        const currentSlide = track.querySelector('.current-slide');
        const nextSlide = currentSlide.nextElementSibling;
        if (nextSlide) {
            moveToSlide(track, currentSlide, nextSlide);
        } else {
            track.style.transform = `translateX(${initialPosition}px)`;
        }
    } else if (moveX > 50) {
        const currentSlide = track.querySelector('.current-slide');
        const prevSlide = currentSlide.previousElementSibling;
        if (prevSlide) {
            moveToSlide(track, currentSlide, prevSlide);
        } else {
            track.style.transform = `translateX(${initialPosition}px)`;
        }
    } else {
        track.style.transform = `translateX(${initialPosition}px)`;
    }
});