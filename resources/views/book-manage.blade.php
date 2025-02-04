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
    <h1 class="text-container-title">Upload Book</h1>
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
    <h1 class="text-container-title">Manage Books</h1>

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
    @foreach ($data as $book)
      <div class="pdf-item" data-book-id="{{ $book->id }}" data-genre="{{ $book->category->name ?? '' }}">
        <div class="rating-badge">
          <i class='bx bxs-star'></i>
          <span>{{ number_format($book->rating ?? 0, 1) }}</span>
        </div>

        <div class="admin-actions">
          <button class="admin-btn edit-btn" onclick="openEditModal({{ $book->id }})" title="Edit">
            <i class='bx bx-edit-alt'></i>
          </button>
          <form id="deleteForm{{ $book->id }}" action="{{ route('delete', $book->id) }}" method="POST"
            style="display: contents;">
            @csrf
            @method('DELETE')
            <button type="button" class="admin-btn delete-btn" title="Delete"
              onclick="confirmDelete('{{ $book->title }}', '{{ $book->author }}', {{ $book->id }})">
              <i class='bx bx-trash'></i>
            </button>
          </form>
          <a href="{{ route('download', $book->file) }}" class="admin-btn download-btn" title="Download">
            <i class='bx bxs-download'></i>
          </a>
        </div>

        <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
          <img src="" alt="Book Cover" style="width: 100%; height: 100%; object-fit: cover;">
        </div>
        <div class="info-container">
          <h3 class="info-title">{{ $book->title ?? '' }}</h3>
          <p class="info-author">{{ $book->author ?? '' }}</p>
          <p class="info-category">{{ $book->category->name ?? '' }}</p>
          <div class="button-container">
            <a class="view-btn" href="{{ route('view', $book->id) }}">
              <i class='bx bx-book-reader'></i> View
            </a>
            <form action="{{ route('readlater.add', $book->id) }}" method="POST" style="display: contents;">
              @csrf
              <button type="submit" class="readlater-btn">
                <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
              </button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="pagination-container">
    {{ $data->onEachSide(1)->links() }}
  </div>

  {{-- Mobile Modals --}}
  <div class="mobile-modals-container">
    @foreach ($data as $book)
      <div class="mobile-modal" data-book-id="{{ $book->id }}">
        <div class="modal-content">
          <button class="modal-close"><i class='bx bx-x'></i></button>
          <div class="modal-book-info">
            <div class="modal-thumbnail">
              <div class="thumbnail" data-pdfpath="/assets/{{ $book->file }}">
                <img src="" alt="Book Cover" style="width: 100%; height: 100%; object-fit: cover;">
              </div>
            </div>
            <div class="modal-details">
              <h3>{{ $book->title }}</h3>
              <p class="modal-author">{{ $book->author }}</p>
              <p class="modal-category">{{ $book->category->name ?? 'Uncategorized' }}</p>
              <div class="modal-rating">
                <i class='bx bxs-star'></i>
                <span>{{ number_format($book->rating ?? 0, 1) }}</span>
              </div>
            </div>
          </div>
          <div class="modal-buttons">
            <div class="modal-action-row">
              <a class="view-btn" href="{{ route('view', $book->id) }}">
                <i class='bx bx-book-reader'></i> Read Now
              </a>
              <div class="action-buttons-group">
                <form action="{{ route('readlater.add', $book->id) }}" method="POST" style="display: contents;">
                  @csrf
                  <button type="submit" class="action-btn">
                    <i class='bx {{ $book->isInReadLaterOf(auth()->user()) ? 'bxs-bookmark' : 'bx-bookmark' }}'></i>
                  </button>
                </form>
                <button class="action-btn edit-btn"
                  onclick="openEditModal({{ $book->id }}); closeAllMobileModals();">
                  <i class='bx bx-edit-alt'></i>
                </button>
                <a href="{{ route('download', $book->file) }}" class="action-btn download-btn">
                  <i class='bx bxs-download'></i>
                </a>
                <button class="action-btn delete-btn"
                  onclick="confirmDelete('{{ $book->title }}', '{{ $book->author }}', {{ $book->id }}); closeAllMobileModals();">
                  <i class='bx bx-trash'></i>
                </button>
              </div>
            </div>
          </div>
        </div>
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

<script>
  document.querySelector('#editForm .visibility-toggle input[type="checkbox"]').addEventListener('change', function() {
    const label = this.closest('.visibility-toggle').querySelector('.visibility-label');
    label.textContent = this.checked ? 'Public' : 'Private';
  });
</script>

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

  // Add styles
  const style = document.createElement('style');
  style.textContent = `
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
    .thumbnail {
      position: relative;
      background: #2a2a2a;
    }
    .loading-indicator {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #2a2a2a;
    }
    .loading-indicator i {
      font-size: 2rem;
      color: white;
      animation: spin 1s linear infinite;
    }
  `;
  document.head.appendChild(style);

  // Observe thumbnails
  document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(thumbnailDiv => {
    // Add loading indicator to each thumbnail
    thumbnailDiv.innerHTML = `
      <div class="loading-indicator">
        <i class='bx bx-loader-alt'></i>
      </div>
    `;
    observer.observe(thumbnailDiv);
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



<script>
  // Book Search
  $(document).ready(function() {
    let searchTimeout;

    $("#search-input").on("keyup", function() {
      clearTimeout(searchTimeout);
      const searchTerm = $(this).val().toLowerCase();

      searchTimeout = setTimeout(() => {
        const currentUrl = new URL(window.location.href);
        currentUrl.searchParams.set('query', searchTerm);
        currentUrl.searchParams.delete('page'); // Reset to first page when searching
        window.location.href = currentUrl.toString();
      }, 500);
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

    const currentUrl = new URL(window.location.href);
    if (selectedGenres.length > 0) {
      currentUrl.searchParams.set('genres', selectedGenres.join(','));
    } else {
      currentUrl.searchParams.delete('genres');
    }
    currentUrl.searchParams.delete('page'); // Reset to first page when filtering
    window.location.href = currentUrl.toString();
  }

  // Handle dropdown click
  document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.querySelector('.dropdown-btn');
    const dropdownContent = document.querySelector('.dropdown-content');

    dropdownBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdownContent.classList.toggle('show');
    });

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.matches('.dropdown-btn') && !e.target.closest('.dropdown-content')) {
        dropdownContent.classList.remove('show');
      }
    });

    // Prevent dropdown from closing when clicking inside
    dropdownContent.addEventListener('click', function(e) {
      e.stopPropagation();
    });
  });

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

    // Reset to first page when filtering
    currentUrl.searchParams.delete('page');

    window.location.href = currentUrl.toString();
  }

  // Update search form to maintain visibility filter
  document.querySelector('form[role="search"]')?.addEventListener('submit', function(e) {
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
  window.onclick = function(event) {
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

  fileInput.addEventListener('change', function() {
    if (this.files && this.files[0]) {
      updateFileInfo(this.files[0]);
    } else {
      updateFileInfo(null);
    }
  });

  document.querySelector('.upload-book-form').addEventListener('submit', function(e) {
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
    toggle.addEventListener('change', function() {
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

<script>
  document.addEventListener('DOMContentLoaded', function() {
    const isMobile = window.innerWidth <= 768;

    if (isMobile) {
      document.querySelectorAll('.pdf-item').forEach(item => {
        item.addEventListener('click', function(e) {
          if (e.target.closest('.view-btn') || e.target.closest('.readlater-btn')) {
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

      document.querySelectorAll('.modal-close').forEach(button => {
        button.addEventListener('click', function(e) {
          e.stopPropagation();
          closeAllMobileModals();
        });
      });

      document.querySelectorAll('.mobile-modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
          if (e.target === this) {
            closeAllMobileModals();
          }
        });
      });
    }

    window.addEventListener('resize', function() {
      const isMobile = window.innerWidth <= 768;
      if (!isMobile) {
        closeAllMobileModals();
      }
    });
  });

  function closeAllMobileModals() {
    document.querySelectorAll('.mobile-modal.active').forEach(modal => {
      modal.classList.remove('active');
    });
    document.body.style.overflow = '';
  }
</script>

</body>

</html>
