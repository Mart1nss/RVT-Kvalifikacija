
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Upload Page</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css" integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

  <style>
  .upload-div {
    display: block;
    width: 400px;
    @media (max-width: 769px) {
    width: 100%;
  }
  }


  .upload-text {
    color: white;
    text-transform: uppercase;
    font-weight: 800;
    margin-bottom: 20px;
  }
  .upload-book-form {
    border: rgb(255, 255, 255) 1px solid;
    background-color: #1c1a1a;
    padding: 15px;
    border-radius: 8px;
    display: block;
    color: white;
  }
  .form-group {
  margin-bottom: 10px;
}

  .form-control {
  width: 100%;
  height: 38px;
  background-color: rgb(37, 37, 37);
  border: none;
  border-radius: 20px;
  font-size: 12px;
  outline: transparent;
  text-align: center;
  color: white;
}

.btn-primary {
  margin-top: 20px;
  border: 1px solid white;
  background-color: white;
  height: 38px;
  width: 100%;
  border-radius: 20px;
  font-weight: 800;
  cursor: pointer;
  font-size: 12px;
  text-transform: uppercase;
  transition: all 0.15;
}

.login-btn {
  border: 1px solid white;
  background-color: black;
  color: white;
  padding: 10px;
  height: 40px;
  width: 120px;
  border-radius: 20px;
  font-weight: 800;
  margin-left: 20px;
  font-size: 12px;
  text-transform: uppercase;
  transition: all 0.15;
}

.remove-btn {
  color: rgb(255, 0, 0);
  text-decoration: none;
  border: rgb(255, 0, 0) 1px solid;
  border-radius: 20px;
  padding: 10px;
  font-family: sans-serif;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  background-color: #1a1a1a;
  cursor: pointer;
}

.download-btn {
  color: white;
  text-decoration: none;
  border: white 1px solid;
  border-radius: 20px;
  padding: 10px;
  font-family: sans-serif;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
}

.view-btn {
  border: 1px solid rgb(0, 0, 0);
  background-color: rgb(255, 255, 255);
  color: rgb(0, 0, 0);
  padding: 10px;
  border-radius: 20px;
  font-weight: 800;
  font-size: 12px;
  text-transform: uppercase;
  text-decoration: none;
}

.item-container {
      background-color: rgb(37, 37, 37);
      border-bottom-left-radius: 10px;
      border-bottom-right-radius: 10px;
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); 
      grid-gap: 20px; 
      justify-content: start; 
      padding: 20px; 
      transition: height 0.3s ease-in-out;
  }

  @media (max-width: 600px) {
  .item-container {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
  }
}

.item-container3 {
      background-color: rgb(37, 37, 37);
      border-bottom-left-radius: 10px;
      border-bottom-right-radius: 10px;
      display: flex;
      grid-gap: 20px; 
      justify-content: start; 
      padding: 20px; 
  }

.item-card {
  background-color: #1c1a1a;
      color: white;
      border-radius: 10px;
      border: white 1px solid;
      overflow: hidden;
      position: relative;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: 0.15s;
  }

  .pdf-item {
      background-color: #1c1a1a;
      color: white;
      border-radius: 10px;
      border: white 1px solid;
      overflow: hidden;
      position: relative;
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      transition: 0.15s;
  }

  .pdf-item:hover {
    transform: scale(1.02); 
    box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2); 
}

.favorite-btn {
      display: none;
      position: absolute;
      background-color: rgb(37, 37, 37);
      color: white;
      border: none;
      top: 0;
      right: 0;
      margin-right: 10px;
      margin-top: 10px;
      padding: 5px;
      height: 40px;
      width: 40px;
      border-radius: 20px;
      font-weight: 800;
      margin-left: 20px;
      font-size: 16px;
      text-transform: uppercase;
      transition: all 0.15;
    }

    .favorite-btn:hover {
      background-color: rgb(56, 56, 56);
      cursor: pointer;
    }

    .button-container {
      display: flex;
      bottom: 0;
      left: 0;
      margin-bottom: 10px;
  }

  .thumbnail {

  }
  
  .thumbnail img {
  max-width: 100%; 
  height: auto;  
  width: 100%; 
}

.info-container {
  display: none; 
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 10px;
  background-color: rgba(0, 0, 0, 0.8); 
  border-radius: 0 0 10px 10px; 
}

.pdf-item:hover .info-container {
  display: block; 
}

.pdf-item:hover .favorite-btn {
  display: block; 
}

.modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgb(0,0,0);
      background-color: rgba(0,0,0,0.4);
      padding-top: 60px;
    }

    .modal-content {
        background-color: rgb(28, 26, 26);
        margin: 5% auto;
        padding: 20px;
        border: 1px solid #ffffff;
        border-radius: 10px;
        width: 80%;
        max-width: 600px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.3);
        z-index: 1001;
    }

    .close-btn {
      color: #ffffff;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close-btn:hover,
    .close-btn:focus {
      opacity: 0.7;
      color: rgb(255, 255, 255);
      text-decoration: none;
      cursor: pointer;
    }

    .modal label {
        display: block;
        margin: 10px 0 5px;
        color: white;
        font-family: sans-serif;
        text-transform: uppercase;
        font-weight: 700;
    }

    .modal input[type="text"] {
        width: calc(100% - 22px);
        padding: 10px;
        margin: 5px 0 20px;
        background-color: rgb(37, 37, 37);
        color: white;
        border: none;
        border-radius: 10px;
        box-sizing: border-box;
    }

    .modal button {
      border: 1px solid rgb(0, 0, 0);
      background-color: rgb(255, 255, 255);
      color: rgb(0, 0, 0);
      padding: 10px;
      border-radius: 20px;
      font-weight: 800;
      font-size: 12px;
      text-transform: uppercase;
      text-decoration: none;
    }

    .modal button:hover {
        opacity: 0.7;
        cursor: pointer;
    }

    .edit-btn {
      color: white;
      background-color: #1a1a1a;
      text-decoration: none;
      border: white 1px solid;
      border-radius: 20px;
      padding: 10px;
      font-family: sans-serif;
      font-weight: 800;
      font-size: 12px;
      text-transform: uppercase;
      cursor: pointer;
    }

    .edit-btn:hover {
      opacity: 0.7;
    }

    .fade-out {
      opacity: 0;
      transition: opacity 0.5s ease-in-out; 
}

#trash-icon {
  font-size: 16px;
}

#download-icon {
  font-size: 16px;
}

.remove-btn2 {
    display: inline-flex;
    background-color: #1a1a1a;
    color: rgb(255, 0, 0);
    border: red 1px solid;
    padding: 5px;
    width: 40px;
    height: 40px;
    margin-left: 5px;
    justify-content: center;
    align-items: center;
    border-radius: 20px;
    font-weight: 800;
    font-size: 16px;
    text-transform: uppercase;
    transition: all 0.15;
  }
  .remove-btn2:hover {
    opacity: 0.7;
    cursor: pointer;
  }

  </style>
</head>


@include('navbar')

  <div class="main-container">
    @if ($errors->any())
    <div class="alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="error-div">
  @if(session('success'))
  <div class="alert alert-success">
      {{ session('success') }}
  </div>
@endif

@if(session('error'))
  <div class="alert alert-danger">
      {{ session('error') }}
  </div>
@endif
  </div>

    
    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Upload Book</h1>
      
    </div>
    <div class="item-container3">

      <div class="upload-div">
    
      <form class="upload-book-form" action="{{url('uploadbook')}}" method="post" enctype="multipart/form-data"> 

        @csrf
        <div class="form-group" style="margin-bottom: 18px;">
          <input class="form-control" style="height: 42px;" type="text" name="title" placeholder="Title">
        </div>
        <div class="form-group" style="margin-bottom: 18px;">
          <input class="form-control" style="height: 42px;" type="text" name="author" placeholder="Author">
        </div>
        <div class="form-group" style="margin-bottom: 18px;">
          <select class="form-control" name="category" style="height: 42px; color:gray;">
              <option  value="">Select Category</option>
              <option  value="Business/Career">Business/Career</option>
              <option  value="Money/Investments">Money/Investments</option>
              <option  value="Sales/Negotiation">Sales/Negotiation</option>
              <option  value="Happieness">Happieness</option>
              <option  value="Productivity">Productivity</option>
              <option  value="Health">Health</option>
              <option  value="Psychology">Psychology</option>
          </select>
      </div>

        <p style="color: white; font-family: sans-serif; text-transform: uppercase; font-weight: 700; margin-bottom: 10px;">max file size <span style="color: rgb(255, 230, 0);">10 mb<span></p>
        <input type="file" name="file">

        <div class="form-btn">
          <input class="btn-primary" type="submit" value="UPLOAD">
        </div>

      </form>

    </div>
  </div>

    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Manage Books</h1>
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

    <div style="margin-bottom: 20px;" class="item-container">

      @foreach ($data as $data)
        <div class="pdf-item" data-genre="{{ $data->category }}">
          <div class="thumbnail" data-pdfpath="/assets/{{ $data->file }}" ></div>
          <div class="info-container"> 
          <h5 style="margin-bottom: 10px; font-size: 20px;  color: rgb(255, 255, 255);">{{$data->title ?? ''}}</h5>
          <h5 style="margin-bottom: 10px; font-size: 16px;  color: rgb(255, 255, 255);">{{$data->author ?? ''}}</h5>
          <h5 style="margin-bottom: 10px; font-size: 14px;  color: rgb(255, 255, 255);"> {{$data->category ?? ''}}</h5>
          <div class="button-container" style="display: flex; justify-content: space-between;">
            <a class="view-btn" href="{{route('view', $data->id)}}">View</a>
            <button style="margin-left: 5px;" class="edit-btn" onclick="openEditModal({{ $data->id }})">EDIT</button>
            <form action="{{ route('delete', $data->id) }}" method="POST">
              @csrf
              @method('DELETE')
              <button class="remove-btn2" type="submit"><i id="trash-icon" class='bx bx-trash'></i></button>
            </form>
            
          </div>
        </div>
        
          <form action="{{ route('favorites.add', $data->id) }}" method="POST">
            @csrf
            <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
          </form>
          <a style="padding-left: 12px; padding-top: 12px; margin-top: 60px; align-items: center; justify-content: center;" class="favorite-btn" href="{{route('download', $data->file)}}"><i id="download-icon" class='bx bxs-download' ></i></a>
          </div>
          
        @endforeach


    </div>

        <!-- Edit Modal -->
    <div id="editModal" class="modal">
      <div class="modal-content">
          <span class="close-btn" onclick="closeEditModal()">&times;</span>
          <form id="editForm" method="POST">
              @csrf
              <label for="title">Title:</label>
              <input type="text" id="title" name="title" required>
              <label for="author">Author:</label>
              <input type="text" id="author" name="author" required>
              <label for="category">Category:</label>
              <input type="text" id="category" name="category" required>
              <button type="submit">Save</button>
          </form>
      </div>
  </div>



  </div>


  <script>
    function fadeOutAndRemove(element) {
        element.classList.add('fade-out'); 
        setTimeout(function() {
            element.remove();
        }, 1500); 
    }

    window.addEventListener('load', function() {
        var alerts = document.querySelectorAll('.alert'); 
        alerts.forEach(function(alert) {
            setTimeout(function() {
                fadeOutAndRemove(alert);
            }, 1500); 
        });
    });
</script>


  <script>
    function openEditModal(id) {
        fetch(`/edit/${id}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('title').value = data.title;
                document.getElementById('author').value = data.author;
                document.getElementById('category').value = data.category;
                document.getElementById('editForm').action = `/update/${id}`;
                document.getElementById('editModal').style.display = 'block';
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }
</script>


  <script type="module">
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
            // Create an img element
            var thumbnailImg = document.createElement('img');
            thumbnailImg.src = canvas.toDataURL(); // Set the image source

            var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' + pdfPath + '"]');
            thumbnailDiv.innerHTML = ''; // Clear any previous content
            thumbnailDiv.appendChild(thumbnailImg); // Add the img element
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