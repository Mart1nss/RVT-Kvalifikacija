<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Upload Page</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/product-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/modal-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/confirmation-modal-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css"
        integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

</head>




@include('components.alert')
@include('navbar')

<div class="main-container">



    <div class="text-container">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <h1 class="manage-books-title">Upload Book</h1>
            <a href="{{ route('categories.index') }}" class="category-link">
                MANAGE CATEGORIES</a>
        </div>
    </div>
    <div class="item-container3">

        <div class="upload-div">

            <form class="upload-book-form" action="{{ url('uploadbook') }}" method="post"
                enctype="multipart/form-data">

                @csrf
                <div class="form-group" style="margin-bottom: 18px;">
                    <input class="form-control" style="height: 42px;" type="text" name="title" placeholder="Title">
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <input class="form-control" style="height: 42px;" type="text" name="author"
                        placeholder="Author">
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <select class="form-control" name="category_id" style="height: 42px; color:gray;">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <p class="max-file-size-text">max file size <span class="highlight-text">10 mb </span>/ format: <span
                        class="highlight-text">pdf</span></p>
                <input type="file" name="file">

                <div class="form-btn">
                    <input class="btn-primary" type="submit" value="UPLOAD">
                </div>

            </form>

        </div>
    </div>

    <div class="text-container">
        <h1 class="manage-books-title">Manage Books</h1>
        <div class="search-filter-container">
            <div class="search-container">
                <input type="text" id="search-input" placeholder="Search books...">
            </div>
            <div class="genre-filter-container">
                <div class="genre-dropdown">
                    <button class="dropdown-btn">Filter by Genres</button>
                    <ul class="dropdown-content">
                        <!-- genres will be populated by JavaScript -->
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 20px;" class="item-container">

        @foreach ($data as $data)
            <div class="pdf-item" data-genre="{{ $data->category->name ?? '' }}">
                <div class="thumbnail" data-pdfpath="/assets/{{ $data->file }}"></div>
                <div class="info-container">
                    <h5 class="info-title" style="margin-bottom: 10px; font-size: 20px; color: rgb(255, 255, 255);">
                        {{ $data->title ?? '' }}</h5>
                    <h5 class="info-author" style="margin-bottom: 10px; font-size: 16px; color: rgb(255, 255, 255);">
                        {{ $data->author ?? '' }}</h5>
                    <h5 class="info-category" style="margin-bottom: 10px; font-size: 14px; color: rgb(255, 255, 255);">
                        {{ $data->category->name ?? '' }}</h5>
                    <div class="button-container" style="display: flex; justify-content: space-between;">
                        <a class="view-btn" href="{{ route('view', $data->id) }}">View</a>
                        <button style="margin-left: 5px;" class="edit-btn"
                            onclick="openEditModal({{ $data->id }})">EDIT</button>
                        <form id="deleteForm{{ $data->id }}" action="{{ route('delete', $data->id) }}"
                            method="POST" style="margin: 0;">
                            @csrf
                            @method('DELETE')
                            <button type="button" class="remove-btn2"
                                onclick="confirmDelete('{{ $data->title }}', '{{ $data->author }}', {{ $data->id }})"><i
                                    class='bx bx-trash'></i></button>
                        </form>
                    </div>
                </div>

                <form action="{{ route('favorites.add', $data->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
                </form>
                <a style="padding-left: 12px; padding-top: 12px; margin-top: 60px; align-items: center; justify-content: center;"
                    class="favorite-btn" href="{{ route('download', $data->file) }}"><i id="download-icon"
                        class='bx bxs-download'></i></a>
            </div>
        @endforeach


    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="edit-modal">
        <div class="edit-modal-content">
            <div class="edit-modal-header">
                <h2>Edit Book</h2>
            </div>
            <form id="editForm" method="POST">
                @csrf
                <input type="hidden" name="_method" value="PUT">
                <label for="title">Title:</label>
                <input type="text" id="title" name="title" required>
                <label for="author">Author:</label>
                <input type="text" id="author" name="author" required>
                <label for="category_id">Category:</label>
                <select id="category_id" name="category_id" class="form-control" required>
                    <option value="">Select Category</option>
                    @foreach ($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <div class="modal-footer">
                    <button type="button" class="edit-btn-secondary" onclick="closeEditModal()">CANCEL</button>
                    <button type="submit" class="edit-btn-primary">SAVE</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Delete Book</h2>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete <span id="deleteBookDetails"></span> book?</p>
                <p class="confirmation-text">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
                <button type="button" class="btn-delete" id="confirmDeleteBtn"
                    onclick="submitDelete()">Delete</button>
            </div>
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
                document.getElementById('category_id').value = data.category_id || '';

                // Update the form action
                document.getElementById('editForm').action = `/update/${id}`;
                document.getElementById('editModal').style.display = 'block';
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading the book details');
            });
    }

    function closeEditModal() {
        document.getElementById('editModal').style.display = 'none';
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        const deleteModal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeEditModal();
        } else if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }
</script>


<script type="module">
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

    function generateThumbnail(pdfPath) {
        pdfjsLib.getDocument(pdfPath).promise.then(function(pdf) {
            pdf.getPage(1).then(function(page) {
                var scale = 1;
                var viewport = page.getViewport({
                    scale: scale
                });
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

                    var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' +
                        pdfPath + '"]');
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
            const genre = book.dataset.genre;
            if (selectedGenres.length === 0 || selectedGenres.includes(genre)) {
                book.style.display = '';
            } else {
                book.style.display = 'none';
            }
        });
    }

    // Fetch and populate genres
    fetch('/get-genres')
        .then(response => response.json())
        .then(genres => {
            const dropdownContent = document.querySelector('.dropdown-content');
            genres.forEach(genre => {
                if (genre) { // Only add non-null genres
                    const li = document.createElement('li');
                    li.innerHTML = `
                      <label>
                          <input type="checkbox" value="${genre}" onchange="filterBooks()">
                          ${genre}
                      </label>
                  `;
                    dropdownContent.appendChild(li);
                }
            });
        });
</script>

<script>
    let currentForm = null;

    function confirmDelete(title, author, id) {
        currentDeleteForm = document.getElementById('deleteForm' + id);
        document.getElementById('deleteBookDetails').textContent = `${title} by ${author}`;
        document.getElementById('deleteModal').style.display = 'block';
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').style.display = 'none';
        currentDeleteForm = null;
    }

    function submitDelete() {
        if (currentDeleteForm) {
            currentDeleteForm.submit();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeDeleteModal();
        }
    }
</script>

</body>

</html>
