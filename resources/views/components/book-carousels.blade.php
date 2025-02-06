 {{-- Newest Books Carousel --}}
 <div class="newest-books-container">
   <div class="carousel-header">
     <h1 class="h1-text">NEWEST</h1>
     <div class="carousel-nav">
       <button class="carousel-button prev"><i class='bx bx-chevron-left'></i></button>
       <button class="carousel-button next"><i class='bx bx-chevron-right'></i></button>
     </div>
   </div>
   <div class="carousel-container">
     <div class="carousel-wrapper">
       @foreach ($recentBooks as $book)
         <div class="carousel-item" data-book-id="{{ $book->id }}">
           <div class="rating-badge">
             <i class='bx bxs-star'></i>
             <span>{{ number_format($book->rating ?? 0, 1) }}</span>
           </div>
           <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
             <div class="loading-indicator">
               <i class='bx bx-loader-alt'></i>
             </div>
           </div>
           <div class="info-container">
             <h3 class="info-title">{{ $book->title }}</h3>
             <p class="info-author">{{ $book->author }}</p>
             <p class="info-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
             <div class="button-container">
               <a class="view-btn" href="{{ route('view', $book->id) }}">
                 <i class='bx bx-book-reader'></i> View
               </a>
               <form
                 action="{{ $book->isInReadLaterOf(auth()->user()) ? route('readlater.delete', $book->id) : route('readlater.add', $book->id) }}"
                 method="POST" style="display: contents;">
                 @csrf
                 @if ($book->isInReadLaterOf(auth()->user()))
                   @method('DELETE')
                 @endif
                 <button type="submit" class="favorite-btn">
                   <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
                 </button>
               </form>
             </div>
           </div>
         </div>
       @endforeach
     </div>
   </div>
 </div>

 {{-- Genre-specific Carousels --}}
 @foreach ($preferredBooks as $genre => $books)
   <div class="newest-books-container">
     <div class="carousel-header">
       <h1 class="h1-text">{{ strtoupper($genre) }} BOOKS</h1>
       <div class="carousel-nav">
         <button class="carousel-button prev"><i class='bx bx-chevron-left'></i></button>
         <button class="carousel-button next"><i class='bx bx-chevron-right'></i></button>
       </div>
     </div>
     <div class="carousel-container">
       <div class="carousel-wrapper">
         @foreach ($books as $book)
           <div class="carousel-item" data-book-id="{{ $book->id }}">
             <div class="rating-badge">
               <i class='bx bxs-star'></i>
               <span>{{ number_format($book->rating ?? 0, 1) }}</span>
             </div>
             <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
               <div class="loading-indicator">
                 <i class='bx bx-loader-alt'></i>
               </div>
             </div>
             <div class="info-container">
               <h3 class="info-title">{{ $book->title }}</h3>
               <p class="info-author">{{ $book->author }}</p>
               <p class="info-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
               <div class="button-container">
                 <a class="view-btn" href="{{ route('view', $book->id) }}">
                   <i class='bx bx-book-reader'></i> View
                 </a>
                 <form
                   action="{{ $book->isInReadLaterOf(auth()->user()) ? route('readlater.delete', $book->id) : route('readlater.add', $book->id) }}"
                   method="POST" style="display: contents;">
                   @csrf
                   @if ($book->isInReadLaterOf(auth()->user()))
                     @method('DELETE')
                   @endif
                   <button type="submit" class="favorite-btn">
                     <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
                   </button>
                 </form>
               </div>
             </div>
           </div>
         @endforeach
       </div>
     </div>
   </div>
 @endforeach

 {{-- Mobile Modals --}}
 <div class="mobile-modals-container">
   @foreach ($recentBooks as $book)
     @include('components.book-modal', ['book' => $book, 'showAdminActions' => false])
   @endforeach

   @foreach ($preferredBooks as $books)
     @foreach ($books as $book)
       @include('components.book-modal', ['book' => $book, 'showAdminActions' => false])
     @endforeach
   @endforeach
 </div>

 {{-- PDF Thumbnail Generation Script --}}
 <script type="module">
   import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';

   pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

   // Track which thumbnails have been generated and are loading
   const generatedThumbnails = new Set();
   const loadingThumbnails = new Set();
   const failedThumbnails = new Set();

   // Intersection Observer for lazy loading
   const observer = new IntersectionObserver((entries) => {
     entries.forEach(entry => {
       if (entry.isIntersecting) {
         const thumbnailDiv = entry.target;
         const pdfPath = thumbnailDiv.dataset.pdfpath;

         if (!generatedThumbnails.has(pdfPath) && !loadingThumbnails.has(pdfPath) && !failedThumbnails.has(
             pdfPath)) {
           loadingThumbnails.add(pdfPath);
           generateThumbnail(pdfPath);
         }
       }
     });
   }, {
     rootMargin: '100px 0px', // Preload margin
     threshold: 0.1 // Trigger when 10% visible
   });

   async function generateThumbnail(pdfPath) {
     try {
       const loadingTask = pdfjsLib.getDocument(pdfPath);
       let timeoutId = setTimeout(() => {
         loadingTask.destroy();
         handleThumbnailError(pdfPath, new Error('Loading timeout'));
       }, 10000); // 10 second timeout

       const pdf = await loadingTask.promise;
       clearTimeout(timeoutId);

       const page = await pdf.getPage(1);

       // Calculate optimal dimensions
       const viewport = page.getViewport({
         scale: 1.0
       });
       const MAX_WIDTH = 400;
       const scale = Math.min(1.0, MAX_WIDTH / viewport.width);
       const scaledViewport = page.getViewport({
         scale
       });

       const canvas = document.createElement('canvas');
       canvas.width = scaledViewport.width;
       canvas.height = scaledViewport.height;

       const context = canvas.getContext('2d', {
         alpha: false,
         desynchronized: true
       });

       await page.render({
         canvasContext: context,
         viewport: scaledViewport,
         intent: 'display'
       }).promise;

       // Create and optimize thumbnail
       const thumbnailImg = new Image();
       thumbnailImg.decoding = 'async';
       thumbnailImg.loading = 'lazy';
       thumbnailImg.style.cssText = 'width: 100%; height: 100%; object-fit: cover;';

       // Use better quality JPEG with reasonable compression
       thumbnailImg.src = canvas.toDataURL('image/jpeg', 0.85);

       // Update all instances of this thumbnail
       document.querySelectorAll(`.thumbnail[data-pdfpath="${pdfPath}"]`).forEach(div => {
         div.innerHTML = '';
         div.appendChild(thumbnailImg.cloneNode(true));
       });

       // Cleanup
       canvas.width = 0;
       canvas.height = 0;
       context.clearRect(0, 0, 0, 0);
       page.cleanup();
       pdf.destroy();
       loadingTask.destroy();

       generatedThumbnails.add(pdfPath);
       loadingThumbnails.delete(pdfPath);

     } catch (error) {
       handleThumbnailError(pdfPath, error);
     }
   }

   function handleThumbnailError(pdfPath, error) {
     console.error("Error generating thumbnail:", error);
     document.querySelectorAll(`.thumbnail[data-pdfpath="${pdfPath}"]`).forEach(div => {
       div.innerHTML = `
        <div style="width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: #2a2a2a;">
          <i class='bx bx-error' style="font-size: 2rem; color: #ff4444;"></i>
        </div>
      `;
     });
     loadingThumbnails.delete(pdfPath);
     failedThumbnails.add(pdfPath);
   }

   // Initialize carousels
   document.addEventListener('DOMContentLoaded', function() {
     const carousels = document.querySelectorAll('.carousel-container');

     carousels.forEach(carousel => {
       const wrapper = carousel.querySelector('.carousel-wrapper');
       const items = wrapper.querySelectorAll('.carousel-item');
       const prevBtn = carousel.parentElement.querySelector('.prev');
       const nextBtn = carousel.parentElement.querySelector('.next');

       let currentPosition = 0;

       function calculateItemsPerView() {
         const carouselWidth = carousel.offsetWidth;
         if (window.innerWidth <= 576) {
           return 3; // Show 3 items on mobile
         } else if (window.innerWidth <= 768) {
           return 4; // Show 4 items on tablets
         } else if (window.innerWidth <= 992) {
           return 3; // Show 3 items on smaller desktops
         } else if (window.innerWidth <= 1200) {
           return 4; // Show 4 items on medium desktops
         } else {
           return 5; // Show 5 items on large desktops
         }
       }

       function updateCarousel() {
         // Ensure we don't scroll past the last item
         const itemsPerView = calculateItemsPerView();
         const itemWidth = (carousel.offsetWidth - (itemsPerView - 1) * 20) / itemsPerView; // Account for gap
         const maxScroll = -(Math.max(0, Math.ceil((items.length - itemsPerView) * (itemWidth + 20))));

         // Prevent scrolling past the end
         if (currentPosition < maxScroll) {
           currentPosition = maxScroll;
         }

         // Prevent scrolling past the start
         if (currentPosition > 0) {
           currentPosition = 0;
         }

         wrapper.style.transform = `translateX(${currentPosition}px)`;
       }

       function moveCarousel(direction) {
         const itemsPerView = calculateItemsPerView();
         const itemWidth = (carousel.offsetWidth - (itemsPerView - 1) * 20) / itemsPerView;
         const moveBy = Math.floor(itemsPerView / 2) * (itemWidth + 20); // Move by half the visible items

         if (direction === 'prev') {
           currentPosition += moveBy;
         } else {
           currentPosition -= moveBy;
         }

         updateCarousel();
       }

       prevBtn.addEventListener('click', () => moveCarousel('prev'));
       nextBtn.addEventListener('click', () => moveCarousel('next'));

       // Handle window resize
       let resizeTimeout;
       window.addEventListener('resize', () => {
         clearTimeout(resizeTimeout);
         resizeTimeout = setTimeout(() => {
           updateCarousel();
         }, 100);
       });

       // Initial update
       updateCarousel();

       // Observe thumbnails in this carousel
       items.forEach(item => {
         const thumbnail = item.querySelector('.thumbnail');
         if (thumbnail) {
           observer.observe(thumbnail);
         }
       });

       // Handle mobile interactions
       if (window.innerWidth <= 768) {
         items.forEach(item => {
           item.addEventListener('click', function(e) {
             if (e.target.closest('.view-btn') || e.target.closest('.favorite-btn')) {
               return;
             }

             const bookId = this.dataset.bookId;
             const modal = document.querySelector(`.mobile-modal[data-book-id="${bookId}"]`);

             if (modal) {
               modal.classList.add('active');
               document.body.style.overflow = 'hidden';
             }
           });
         });
       }
     });

     // Mobile modal handling
     document.querySelectorAll('.modal-close').forEach(button => {
       button.addEventListener('click', function(e) {
         e.stopPropagation();
         const modal = this.closest('.mobile-modal');
         if (modal) {
           modal.classList.remove('active');
           document.body.style.overflow = '';
         }
       });
     });

     document.querySelectorAll('.mobile-modal').forEach(modal => {
       modal.addEventListener('click', function(e) {
         if (e.target === this) {
           this.classList.remove('active');
           document.body.style.overflow = '';
         }
       });
     });
   });

   // Memory management
   let cleanupInterval;
   const MAX_CACHED_THUMBNAILS = 50;

   function startCleanupInterval() {
     cleanupInterval = setInterval(() => {
       if (generatedThumbnails.size > MAX_CACHED_THUMBNAILS) {
         const thumbnailsToRemove = Array.from(generatedThumbnails).slice(0, 20);
         thumbnailsToRemove.forEach(path => {
           generatedThumbnails.delete(path);
           document.querySelectorAll(`.thumbnail[data-pdfpath="${path}"]`).forEach(div => {
             if (!div.isIntersecting) {
               div.innerHTML = `
                <div class="loading-indicator">
                  <i class='bx bx-loader-alt'></i>
                </div>
              `;
             }
           });
         });
       }
     }, 30000); // Check every 30 seconds
   }

   // Visibility handling
   document.addEventListener('visibilitychange', () => {
     if (document.hidden) {
       clearInterval(cleanupInterval);
     } else {
       startCleanupInterval();
     }
   });

   startCleanupInterval();

   // Cleanup on page leave
   window.addEventListener('unload', () => {
     observer.disconnect();
     generatedThumbnails.clear();
     loadingThumbnails.clear();
     failedThumbnails.clear();
     clearInterval(cleanupInterval);
   });
 </script>
