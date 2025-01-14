{{-- Newest Books Carousel --}}
<div class="newest-books-container">
    <h1 class="h1-text">NEWEST BOOKS</h1>
    <div class="carousel-container">
        <button class="carousel-button prev"><i class='bx bx-chevron-left'></i></button>
        <div class="carousel-wrapper">
            @foreach($recentBooks as $book)
            <div class="carousel-item">
                <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
                <div class="info-container">
                    <h3 class="info-title">{{ $book->title }}</h3>
                    <p class="info-author">{{ $book->author }}</p>
                    <p class="info-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
                    <div class="button-container">
                        <a class="view-btn" href="{{ route('view', $book->id) }}">
                            <i class='bx bx-book-reader'></i> View
                        </a>
                        <button class="favorite-btn" onclick="toggleFavorite({{ $book->id }})">
                            <i class='bx bx-heart'></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-button next"><i class='bx bx-chevron-right'></i></button>
    </div>
</div>

{{-- Genre-specific Carousels --}}
@foreach($preferredBooks as $genre => $books)
<div class="newest-books-container">
    <h1 class="h1-text">{{ strtoupper($genre) }} BOOKS</h1>
    <div class="carousel-container">
        <button class="carousel-button prev"><i class='bx bx-chevron-left'></i></button>
        <div class="carousel-wrapper">
            @foreach($books as $book)
            <div class="carousel-item">
                <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}"></div>
                <div class="info-container">
                    <h3 class="info-title">{{ $book->title }}</h3>
                    <p class="info-author">{{ $book->author }}</p>
                    <p class="info-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
                    <div class="button-container">
                        <a class="view-btn" href="{{ route('view', $book->id) }}">
                            <i class='bx bx-book-reader'></i> View
                        </a>
                        <button class="favorite-btn" onclick="toggleFavorite({{ $book->id }})">
                            <i class='bx bx-heart'></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        <button class="carousel-button next"><i class='bx bx-chevron-right'></i></button>
    </div>
</div>
@endforeach

{{-- PDF Thumbnail Generation Script --}}
<script type="module">
    import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';
    
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';
    
    async function generateThumbnail(pdfPath) {
        try {
            const loadingTask = pdfjsLib.getDocument(pdfPath);
            const pdf = await loadingTask.promise;
            const page = await pdf.getPage(1);
            
            const viewport = page.getViewport({ scale: 1 });
            const canvas = document.createElement('canvas');
            const context = canvas.getContext('2d');
            
            canvas.width = viewport.width;
            canvas.height = viewport.height;
            
            const renderContext = {
                canvasContext: context,
                viewport: viewport
            };
            
            await page.render(renderContext).promise;
            
            const thumbnailImg = document.createElement('img');
            thumbnailImg.src = canvas.toDataURL();
            thumbnailImg.style.width = '100%';
            thumbnailImg.style.height = '100%';
            thumbnailImg.style.objectFit = 'cover';
            
            const thumbnailDivs = document.querySelectorAll('.thumbnail[data-pdfpath="' + pdfPath + '"]');
            thumbnailDivs.forEach(div => {
                div.innerHTML = '';
                div.appendChild(thumbnailImg.cloneNode(true));
            });
        } catch (error) {
            console.error("Error loading PDF:", error);
            const thumbnailDivs = document.querySelectorAll('.thumbnail[data-pdfpath="' + pdfPath + '"]');
            thumbnailDivs.forEach(div => {
                div.innerHTML = '<img src="{{ asset("images/pdf-icon.png") }}" style="width: 100%; height: 100%; object-fit: contain; padding: 20px;">';
            });
        }
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        const uniquePdfPaths = new Set();
        
        document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function(thumbnailDiv) {
            uniquePdfPaths.add(thumbnailDiv.dataset.pdfpath);
        });
        
        uniquePdfPaths.forEach(function(pdfPath) {
            generateThumbnail(pdfPath);
        });
    });
</script>
