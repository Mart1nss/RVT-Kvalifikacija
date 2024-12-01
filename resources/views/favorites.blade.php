
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Favorites</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css" integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
#dropdown-3 {
            background-color: rgb(56, 56, 56);
        }
  </style>

</head>
<body>

  @include('navbar')

  <div class="main-container" >

    @if ($errors->any())
    <div class="alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Favorites</h1>
    </div>

    <div class="item-container">

      @if ($favorites->count() > 0) 
      @foreach ($favorites as $favorite)
          <div class="pdf-item">
            <div class="thumbnail" data-pdfpath="/assets/{{ $favorite->product->file }}" ></div>
            <div class="info-container">
              <h5 class="info-title">{{ $favorite->product->title }}</h5> 
              <h5 class="info-author">{{ $favorite->product->author }}</h5>
              <h5 class="info-category">{{ $favorite->product->category }}</h5>
              <div class="button-container">
                <a class="view-btn" href="{{route('view', $favorite->product->id)}}">View</a>
              </div>
            </div>
              <form action="{{ route('favorites.delete', $favorite->product_id) }}" method="POST">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="remove-btn"><i class='bx bx-trash'></i></button>
              </form>
          </div>
      @endforeach
  @else
      <p style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">There are no favorites!</p>
  @endif 
  

    </div>

  </div>

  <script type="module">
    //Book Thumbnails
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';
    function generateThumbnail(pdfPath) {
      pdfjsLib.getDocument(pdfPath).promise.then(function(pdf) {
        pdf.getPage(1).then(function(page) {
          var scale = 1; 
          var viewport = page.getViewport({ scale: scale });
          var canvas = document.createElement('canvas');
          var context = canvas.getContext('2d');
  
          canvas.height = viewport.height;
          canvas.width = viewport.width;
  
          var renderContext = {
            canvasContext: context,
            viewport: viewport
          };
  
          page.render(renderContext).promise.then(function() {
            
            var thumbnailImg = document.createElement('img');
            thumbnailImg.src = canvas.toDataURL(); 

            var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' + pdfPath + '"]');
            thumbnailDiv.innerHTML = ''; 
            thumbnailDiv.appendChild(thumbnailImg); 
          });
        });
      }).catch(function(error) {
          console.error("Error loading PDF:", error);
      });
    }
  
    
    document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function(thumbnailDiv) {
      var pdfPath = thumbnailDiv.dataset.pdfpath;
      generateThumbnail(pdfPath);
    });
  </script>
  
</body>
</html>