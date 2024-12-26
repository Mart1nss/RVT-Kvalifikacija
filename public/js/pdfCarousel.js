document.addEventListener('DOMContentLoaded', () => {
    const track = document.querySelector('.carousel-track');
    const slides = Array.from(track.children);
    
    // Clone slides multiple times to ensure enough content for mobile
    const numberOfClones = 3; // Clone sets multiple times
    
    for (let i = 0; i < numberOfClones; i++) {
        slides.forEach(slide => {
            const clone = slide.cloneNode(true);
            track.appendChild(clone);
        });
    }
});