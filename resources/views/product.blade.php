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
    <style>
        .visibility-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 10px 0;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #252525;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: green;
            transition: .4s;
        }

        input:checked+.slider {
            background-color: white;
        }

        input:checked+.slider:before {
            transform: translateX(26px);
        }

        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }

        .visibility-label {
            font-size: 16px;
        }
    </style>
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

            <form class="upload-book-form" action="{{ url('uploadbook') }}" method="post" enctype="multipart/form-data">

                @csrf
                <div class="form-group" style="margin-bottom: 18px;">
                    <input class="form-control" style="height: 42px;" type="text" name="title" id="titleInput"
                        placeholder="Title">
                    <span class="error-message" id="titleError"></span>
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <input class="form-control" style="height: 42px;" type="text" name="author" id="authorInput"
                        placeholder="Author">
                    <span class="error-message" id="authorError"></span>
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <select class="form-control" name="category_id" id="categoryInput"
                        style="height: 42px; color:gray; cursor: pointer;">
                        <option value="">Select Category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                    <span class="error-message" id="categoryError"></span>
                </div>
                <div class="form-group" style="margin-bottom: 18px;">
                    <div class="visibility-toggle">
                        <label class="switch">
                            <input type="checkbox" name="is_public" checked>
                            <span class="slider round"></span>
                        </label>
                        <span class="visibility-label">Public</span>
                    </div>
                </div>

                <p class="max-file-size-text">max file size <span class="highlight-text">10 mb </span>| format <span
                        class="highlight-text">pdf</span></p>
                <div class="file-input-container">
                    <div class="drop-zone" id="drop-zone">
                        <div class="drop-zone-content">
                            <i class='bx bx-upload'></i>
                            <p>Drag and drop PDF file here or</p>
                            <label for="fileInput" class="custom-file-upload">
                                Choose File
                            </label>
                        </div>
                        <input class="file-input" type="file" name="file" id="fileInput" accept=".pdf">
                    </div>
                    <div class="file-info" id="file-info" style="display: none;">
                        <span id="file-chosen">No file chosen</span>
                        <button type="button" class="clear-file-btn" onclick="clearFileInput()">Clear</button>
                    </div>
                    <span class="error-message" id="fileError"></span>
                </div>

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
            <div class="visibility-filter" style="margin-left: 15px; cursor: pointer;">
                <select id="visibilityFilter" onchange="applyVisibilityFilter()"
                    style="padding: 8px; border-radius: 8px; background: #1c1a1a; color: white; border: none; height: 41px; text-transform: uppercase; font-weight: 800; font-size: 12px;">
                    <option value="all" {{ $visibility == 'all' ? 'selected' : '' }}>All Books</option>
                    <option value="public" {{ $visibility == 'public' ? 'selected' : '' }}>Public Only</option>
                    <option value="private" {{ $visibility == 'private' ? 'selected' : '' }}>Private Only</option>
                </select>
            </div>
        </div>
    </div>

    <div style="margin-bottom: 20px;" class="item-container">

        @foreach ($data as $data)
            <div class="pdf-item" data-genre="{{ $data->category->name ?? '' }}">
                <div class="thumbnail" data-pdfpath="/assets/{{ $data->file }}"></div>
                <div class="info-container">
                    <h5 class="info-title" style="margin-bottom: 10px; font-size: 20px; color: rgb(255, 255, 255);">
                        {{ $data->title ?? '' }}
                    </h5>
                    <h5 class="info-author" style="margin-bottom: 10px; font-size: 16px; color: rgb(255, 255, 255);">
                        {{ $data->author ?? '' }}
                    </h5>
                    <h5 class="info-category" style="margin-bottom: 10px; font-size: 14px; color: rgb(255, 255, 255);">
                        {{ $data->category->name ?? '' }}
                    </h5>
                    <div class="button-container" style="display: flex; justify-content: space-between;">
                        <a class="view-btn" href="{{ route('view', $data->id) }}">View</a>
                        <button style="margin-left: 5px;" class="edit-btn"
                            onclick="openEditModal({{ $data->id }})">EDIT</button>
                        <form id="deleteForm{{ $data->id }}" action="{{ route('delete', $data->id) }}" method="POST"
                            style="margin: 0;">
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
                <div class="visibility-toggle">
                    <label class="switch">
                        <input type="checkbox" name="is_public" id="edit_is_public">
                        <span class="slider round"></span>
                    </label>
                    <span class="visibility-label">Public</span>
                </div>
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
                <button type="button" class="btn-delete" id="confirmDeleteBtn" onclick="submitDelete()">Delete</button>
            </div>
        </div>
    </div>

    <div id="alertContainer"></div>

</div>


<script>
    function fadeOutAndRemove(element) {
        element.classList.add('fade-out');
        setTimeout(function () {
            element.remove();
        }, 1500);
    }

    window.addEventListener('load', function () {
        var alerts = document.querySelectorAll('.alert');
        alerts.forEach(function (alert) {
            setTimeout(function () {
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
                document.getElementById('edit_is_public').checked = data.is_public;
                document.querySelector('#editForm .visibility-label').textContent = data.is_public ? 'Public' : 'Private';
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
    window.onclick = function (event) {
        const modal = document.getElementById('editModal');
        const deleteModal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeEditModal();
        } else if (event.target == deleteModal) {
            closeDeleteModal();
        }
    }
</script>

<script>
    document.querySelector('#editForm .visibility-toggle input[type="checkbox"]').addEventListener('change', function () {
        const label = this.closest('.visibility-toggle').querySelector('.visibility-label');
        label.textContent = this.checked ? 'Public' : 'Private';
    });
</script>

<script type="module">
    pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

    function generateThumbnail(pdfPath) {
        pdfjsLib.getDocument(pdfPath).promise.then(function (pdf) {
            pdf.getPage(1).then(function (page) {
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

                page.render(renderContext).promise.then(function () {
                    // Create an img element
                    var thumbnailImg = document.createElement('img');
                    thumbnailImg.src = canvas.toDataURL(); // Set the image source

                    var thumbnailDiv = document.querySelector('.thumbnail[data-pdfpath="' +
                        pdfPath + '"]');
                    thumbnailDiv.innerHTML = ''; // Clear any previous content
                    thumbnailDiv.appendChild(thumbnailImg); // Add the img element
                });
            });
        }).catch(function (error) {
            console.error("Error loading PDF:", error);
        });
    }


    document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function (thumbnailDiv) {
        var pdfPath = thumbnailDiv.dataset.pdfpath;
        generateThumbnail(pdfPath);
    });
</script>



<script>
    // Book Search
    $(document).ready(function () {
        $("#search-input").on("keyup", function () {
            let searchTerm = $(this).val().toLowerCase();

            $(".pdf-item").each(function () {
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
    function applyVisibilityFilter() {
        const visibility = document.getElementById('visibilityFilter').value;
        const currentUrl = new URL(window.location.href);

        // Update or add visibility parameter
        currentUrl.searchParams.set('visibility', visibility);

        // Keep the search query if it exists
        const searchQuery = document.querySelector('input[name="query"]')?.value;
        if (searchQuery) {
            currentUrl.searchParams.set('query', searchQuery);
        }

        window.location.href = currentUrl.toString();
    }

    // Update search form to maintain visibility filter
    document.querySelector('form[role="search"]')?.addEventListener('submit', function (e) {
        e.preventDefault();
        const visibility = document.getElementById('visibilityFilter').value;
        const searchQuery = this.querySelector('input[name="query"]').value;

        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('visibility', visibility);
        currentUrl.searchParams.set('query', searchQuery);

        window.location.href = currentUrl.toString();
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
    window.onclick = function (event) {
        const modal = document.getElementById('deleteModal');
        if (event.target == modal) {
            closeDeleteModal();
        }
    }
</script>

<script>
    const dropZone = document.getElementById('drop-zone');
    const fileInput = document.getElementById('fileInput');
    const fileInfo = document.getElementById('file-info');
    const fileChosen = document.getElementById('file-chosen');

    // Prevent default drag behaviors
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, preventDefaults, false);
        document.body.addEventListener(eventName, preventDefaults, false);
    });

    // Highlight drop zone when dragging over it
    ['dragenter', 'dragover'].forEach(eventName => {
        dropZone.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropZone.addEventListener(eventName, unhighlight, false);
    });

    // Handle dropped files
    dropZone.addEventListener('drop', handleDrop, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        dropZone.classList.add('dragover');
    }

    function unhighlight(e) {
        dropZone.classList.remove('dragover');
    }

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;

        if (files.length > 0) {
            fileInput.files = files;
            updateFileInfo(files[0]);
        }
    }

    function validateFileType(file) {
        const validTypes = ['application/pdf'];
        if (file && !validTypes.includes(file.type)) {
            dropZone.classList.add('file-error');
            document.getElementById('fileError').textContent = 'Please upload only PDF files';
            return false;
        }
        return true;
    }

    function validateFileSize(file) {
        const maxSize = 10 * 1024 * 1024; // 10MB in bytes
        if (file && file.size > maxSize) {
            dropZone.classList.add('file-error');
            document.getElementById('fileError').textContent = 'File size must be less than 10MB';
            return false;
        }
        return true;
    }

    function updateFileInfo(file) {
        if (file) {
            const isValidType = validateFileType(file);
            const isValidSize = validateFileSize(file);

            if (isValidType && isValidSize) {
                fileChosen.textContent = file.name;
                fileInfo.style.display = 'flex';
                dropZone.style.display = 'none';
                dropZone.classList.remove('file-error');
                document.getElementById('fileError').textContent = '';
            } else {
                fileChosen.textContent = 'Choose or drop PDF file';
                fileInfo.style.display = 'none';
                dropZone.style.display = 'block';
                fileInput.value = ''; // Clear the file input
            }
        } else {
            fileChosen.textContent = 'Choose or drop PDF file';
            fileInfo.style.display = 'none';
            dropZone.style.display = 'block';
            document.getElementById('fileError').textContent = '';
            dropZone.classList.remove('file-error');
        }
    }

    fileInput.addEventListener('change', function () {
        if (this.files && this.files[0]) {
            updateFileInfo(this.files[0]);
        } else {
            updateFileInfo(null);
        }
    });

    document.querySelector('.upload-book-form').addEventListener('submit', function (e) {
        const titleInput = document.getElementById('titleInput');
        const authorInput = document.getElementById('authorInput');
        const categoryInput = document.getElementById('categoryInput');
        const fileInput = document.getElementById('fileInput');

        // Clear all previous error messages
        document.querySelectorAll('.error-message').forEach(el => el.textContent = '');

        let hasError = false;

        // Check title
        if (!titleInput.value.trim()) {
            e.preventDefault();
            document.getElementById('titleError').textContent = 'Please enter the book title';
            hasError = true;
        }

        // Check author
        if (!authorInput.value.trim()) {
            e.preventDefault();
            document.getElementById('authorError').textContent = 'Please enter the author name';
            hasError = true;
        }

        // Check category
        if (!categoryInput.value) {
            e.preventDefault();
            document.getElementById('categoryError').textContent = 'Book genre required';
            hasError = true;
        }

        // Check if file is selected
        if (!fileInput.files[0]) {
            e.preventDefault();
            document.getElementById('fileError').textContent = 'Please select a PDF file';
            dropZone.classList.add('file-error');
            return;
        }

        const file = fileInput.files[0];
        const isValidType = validateFileType(file);
        const isValidSize = validateFileSize(file);

        if (!isValidType || !isValidSize) {
            e.preventDefault();
            return;
        }
    });

    function showFloatingAlert(type, message) {
        const alertContainer = document.getElementById('alertContainer');
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type === 'error' ? 'danger' : type}`;
        alertDiv.textContent = message;

        // Remove any existing alerts
        alertContainer.innerHTML = '';
        alertContainer.appendChild(alertDiv);

        // Remove the alert after 3 seconds
        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }

    function clearFileInput() {
        fileInput.value = '';
        updateFileInfo(null);
    }
</script>

<script>
    document.querySelectorAll('.visibility-toggle input[type="checkbox"]').forEach(toggle => {
        toggle.addEventListener('change', function () {
            const bookId = this.closest('form').dataset.bookId;
            const isPublic = this.checked;
            const label = this.closest('.visibility-toggle').querySelector('.visibility-label');

            label.textContent = isPublic ? 'Public' : 'Private';

            if (bookId) { // Only for existing books
                fetch(`/toggle-visibility/${bookId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => response.json())
                    .then(data => {
                        if (!data.success) {
                            this.checked = !this.checked; // Revert if failed
                            label.textContent = this.checked ? 'Public' : 'Private';
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        this.checked = !this.checked; // Revert on error
                        label.textContent = this.checked ? 'Public' : 'Private';
                    });
            }
        });
    });
</script>

</body>

</html>