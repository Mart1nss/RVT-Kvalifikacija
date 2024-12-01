
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>All Books</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css" integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>

<body>
  @include('navbar')

  <div class="main-container">

@if(session('error'))
    <div class="alert-danger">
        {{ session('error') }}
    </div>
@endif

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="text-container">
  <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">All Books</h1>
  <div class="search-filter-container">
    <div class="search-container">
        <input type="text" id="search-input" placeholder="Search books...">
    </div>
    <div class="genre-filter-container">
        <div class="genre-dropdown">
            <button class="dropdown-btn">Filter by Genres</button>
            <ul class="dropdown-content">
                <!-- zanri -->
            </ul>
        </div>
    </div>
</div>
</div>

  <div class="item-container" >

    @foreach ($data as $data)
      <div class="pdf-item" data-genre="{{ $data->category }}">
        <div class="thumbnail" data-pdfpath="/assets/{{ $data->file }}" ></div>
        <div class="info-container"> 
          <h5 class="info-title" >{{$data->title ?? ''}}</h5>
          <h5 class="info-author" >{{$data->author ?? ''}}</h5>
          <h5 class="info-category" >{{$data->category ?? ''}}</h5>
          <div class="button-container">
            <a class="view-btn" href="{{route('view', $data->id)}}">View</a>
            <a class="download-btn" href="{{route('download', $data->file)}}">Download</a>
          </div>
        </div>
          <form action="{{ route('favorites.add', $data->id) }}" method="POST">
            @csrf
            <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
          </form>
      </div>
      @endforeach
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


<script>
  // Book Search
  $(document).ready(function() {
      $("#search-input").on("keyup", function() {
          let searchTerm = $(this).val().toLowerCase();

          $(".pdf-item").each(function() {
              let title = $(this).find(".info-container h5:first").text().toLowerCase();
              if (title.includes(searchTerm)) {
                  $(this).show();
              } else {
                  $(this).hide();
              }
          });
      });
  });
</script>



<script>
  function filterBooks() {
      const selectedGenres = [];
      const checkboxes = document.querySelectorAll('.dropdown-content input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
          if (checkbox.checked) {
              selectedGenres.push(checkbox.value);
          }
      });
  
      const books = document.querySelectorAll('.pdf-item');
      books.forEach(book => {
          if (selectedGenres.length === 0 || selectedGenres.includes(book.dataset.genre)) {
              book.style.display = 'block';
          } else {
              book.style.display = 'none';
          }
      });
  }
  
  // Add event listener to dropdown content
const dropdownContent = document.querySelector('.dropdown-content');
dropdownContent.addEventListener('click', (event) => {
  const clickedElement = event.target;
  let checkbox;

  // Logic to get checkbox
  if (clickedElement.classList.contains('filter-list') || clickedElement.classList.contains('filter-label')) {
    // If clicked element is  either the list or label, find the checkbox inside
    checkbox = clickedElement.closest('.filter-list').querySelector('input[type="checkbox"]');
  } else if (clickedElement.type === 'checkbox') { 
    // If the clicked element is directly the checkbox
    checkbox = clickedElement; 
  }

  // If we found a checkbox, toggle and filter
  if (checkbox) { 
    checkbox.checked = !checkbox.checked;
    filterBooks();
  }
});
  
  fetch('/get-genres')
      .then(response => response.json())
      .then(genres => {
          const dropdownContent = document.querySelector('.dropdown-content');
          genres.forEach(genre => {
              const listItem = document.createElement('a');
              listItem.className = 'filter-list'; 
              const checkbox = document.createElement('input');
              checkbox.type = 'checkbox';
              checkbox.value = genre;
              checkbox.id = `genre-${genre}`; 
  
              const label = document.createElement('label');
              label.htmlFor = `genre-${genre}`; 
              label.textContent = genre;
              label.className = 'filter-label'; 
  
              listItem.appendChild(checkbox);
              listItem.appendChild(label);
              dropdownContent.appendChild(listItem);
          });
      });
  </script>

  
</body>
</html>