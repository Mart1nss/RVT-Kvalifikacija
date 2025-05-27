
 <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
 <link rel="stylesheet" href="{{ asset('css/pdf-carousel.css') }}">

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
           <div class="thumbnail">
             <img src="{{ asset('book-thumbnails/' . str_replace('.pdf', '.jpg', $book->file)) }}"
               alt="{{ $book->title }}" loading="lazy"
               onload="this.parentElement.querySelector('.loading-indicator').style.display='none';"
               style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
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
                 class="read-later-form"
                 data-book-title="{{ $book->title }}"
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
       <h1 class="h1-text">{{ strtoupper($genre) }}</h1>
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
             <div class="thumbnail">
               <img src="{{ asset('book-thumbnails/' . str_replace('.pdf', '.jpg', $book->file)) }}"
                 alt="{{ $book->title }}" loading="lazy"
                 onload="this.parentElement.querySelector('.loading-indicator').style.display='none';"
                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
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
                 class="read-later-form"
                 data-book-title="{{ $book->title }}"
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
 @php
   $allBooksForModals = collect($recentBooks);
   foreach ($preferredBooks as $genreBooks) {
       $allBooksForModals = $allBooksForModals->merge($genreBooks);
   }
   $uniqueBooksForModals = $allBooksForModals->unique('id');
 @endphp
 <div class="mobile-modals-container">
   @foreach ($uniqueBooksForModals as $book)
     @include('components.book-modal', ['book' => $book, 'showAdminActions' => false])
   @endforeach
 </div>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Handle read later form submissions
    const readLaterForms = document.querySelectorAll('.read-later-form');
    
    readLaterForms.forEach(form => {
      form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const bookTitle = this.getAttribute('data-book-title');
        const formData = new FormData(this);
        const isRemoving = this.querySelector('input[name="_method"]') !== null;
        const button = this.querySelector('.favorite-btn');
        const icon = button.querySelector('i');
        
        fetch(this.action, {
          method: 'POST',
          headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
          },
          body: formData
        })
        .then(response => response.json())
        .then(data => {
          // Toggle bookmark icon
          if (isRemoving) {
            icon.classList.remove('bxs-bookmark');
            icon.classList.add('bx-bookmark');
            this.querySelector('input[name="_method"]').remove();
            this.action = this.action.replace('delete', 'add');
            window.showAlert('Book removed from read later list', 'success');
          } else {
            icon.classList.remove('bx-bookmark');
            icon.classList.add('bxs-bookmark');
            const methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = 'DELETE';
            this.appendChild(methodInput);
            this.action = this.action.replace('add', 'delete');
            window.showAlert('Book added to read later list', 'success');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          window.showAlert('An error occurred. Please try again.', 'error');
        });
      });
    });
  });
</script>
