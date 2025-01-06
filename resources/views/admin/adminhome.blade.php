@include('navbar')

<link rel="stylesheet" href="{{ asset('css/adminhome-style.css') }}">
<link rel="stylesheet" href="{{ asset('css/pdf-carousel.css') }}">
<script src="{{ asset('js/pdf-carousel.js') }}" defer></script>
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>Dashboard</title>

<div class="main-container">

    <div class="text-container">
        <h1 class="h1-text">Admin Dashboard</h1>
    </div>
        
    <div class="item-container">

        <div class="btn-div">

        <h1 style="margin-bottom: 20px; color: white; font-family: sans-serif; font-weight: 800; font-size: 18px;">Welcome, {{ auth()->user()->name }}!</h1>

            <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{'/uploadpage'}}"><i id="dashboardIcon"
                    class='bx bx-cog'></i> Manage Books</a>

            <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{'/managepage'}}"><i id="dashboardIcon"
                    class='bx bx-user'></i> Manage Users</a>

            <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{'/notifications'}}"><i id="dashboardIcon"
                    class='bx bx-bell'></i> notifications</a>
            
            <a style="margin-bottom: 20px;" class="btn-dashboard" href="#"><i id="dashboardIcon" class='bx bx-history'></i> Audit Logs</a>

            <a class="btn-dashboard" href="{{'/bookpage'}}"><i id="dashboardIcon" class='bx bx-book'></i> Library</a>

        </div>

        <div class="stats-div">
            <h1 class="h1-text" style="margin-bottom: 10px;">ANALYTICS</h1>

            <div class="stats-card">
                <p class="stats-text">Total Books: {{ $bookCount }}</p>
                <p class="stats-text">Total Users: {{ $userCount }}</p>
                <p class="stats-text">Currently online: </p>
            </div>

        </div>

    </div>

    <div class="newest-books-container">
        <h2 class="newest-books-title">Newest Books</h2>
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

</div>



<script type="module">
    import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';
    
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';
    
    function generateThumbnail(pdfPath) {
        pdfjsLib.getDocument(pdfPath).promise.then(function(pdf) {
            pdf.getPage(1).then(function(page) {
                var scale = 1.5; // Increased scale for better quality
                var viewport = page.getViewport({ scale: scale });
                var canvas = document.createElement('canvas');
                var context = canvas.getContext('2d');
                
                // Set canvas dimensions to match viewport
                canvas.height = viewport.height;
                canvas.width = viewport.width;
                
                var renderContext = {
                    canvasContext: context,
                    viewport: viewport
                };
                
                page.render(renderContext).promise.then(function() {
                    var thumbnailImg = document.createElement('img');
                    thumbnailImg.src = canvas.toDataURL();
                    thumbnailImg.style.width = '100%';
                    thumbnailImg.style.height = '100%';
                    thumbnailImg.style.objectFit = 'cover';
                    
                    var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' + pdfPath + '"]');
                    if (thumbnailDiv) {
                        thumbnailDiv.innerHTML = '';
                        thumbnailDiv.appendChild(thumbnailImg);
                    }
                });
            });
        }).catch(function(error) {
            console.error("Error loading PDF:", error);
            // Show a fallback image on error
            var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' + pdfPath + '"]');
            if (thumbnailDiv) {
                thumbnailDiv.innerHTML = '<img src="{{ asset("images/pdf-icon.png") }}" style="width: 100%; height: 100%; object-fit: contain; padding: 20px;">';
            }
        });
    }
    
    // Wait for the DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function(thumbnailDiv) {
            var pdfPath = thumbnailDiv.dataset.pdfpath;
            generateThumbnail(pdfPath);
        });
    });
</script>