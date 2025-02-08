<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Upload Page</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/product-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-edit.css') }}">
  <link rel="stylesheet" href="{{ asset('css/modal-confirmation-delete.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/pdf-item.css') }}">
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
    <h1 class="text-container-title">Upload Book</h1>
  </div>
  <div class="item-container3">
    @include('components.book-upload-form')
  </div>

  <div class="text-container">
    <h1 class="text-container-title">Manage Books</h1>

    <div class="search-filter-container">
      <div class="search-container">
        <input type="text" id="search-input" placeholder="Search books...">
      </div>
      <button class="mobile-filter-btn">
        <i class='bx bx-filter-alt'></i>
      </button>
      <div class="genre-filter-container">
        <div class="genre-dropdown">
          <button class="dropdown-btn">Filter by Genres</button>
          <ul class="dropdown-content">
            <!-- Genres will be populated by JavaScript -->
            <div class="dropdown-footer">
              <button type="button" class="clear-filters">Clear</button>
              <button type="button" class="apply-filters">Apply</button>
            </div>
          </ul>
        </div>
      </div>
      <div class="visibility-filter" style="margin-left: 15px;">
        <select id="visibilityFilter" onchange="applyVisibilityFilter()"
          style="padding: 8px; border-radius: 8px; background: #1c1a1a; color: white; border: none; height: 41px; text-transform: uppercase; font-weight: 800; font-size: 12px;">
          <option value="all" {{ $visibility == 'all' ? 'selected' : '' }}>All Books</option>
          <option value="public" {{ $visibility == 'public' ? 'selected' : '' }}>Public Only</option>
          <option value="private" {{ $visibility == 'private' ? 'selected' : '' }}>Private Only</option>
        </select>
      </div>
      <div class="sort-dropdown">
        <select id="sortSelect" onchange="applySorting()">
          <option value="newest" {{ $sort == 'newest' ? 'selected' : '' }}>Newest First</option>
          <option value="oldest" {{ $sort == 'oldest' ? 'selected' : '' }}>Oldest First</option>
          <option value="title_asc" {{ $sort == 'title_asc' ? 'selected' : '' }}>Title (A-Z)</option>
          <option value="title_desc" {{ $sort == 'title_desc' ? 'selected' : '' }}>Title (Z-A)</option>
          <option value="author_asc" {{ $sort == 'author_asc' ? 'selected' : '' }}>Author (A-Z)</option>
          <option value="author_desc" {{ $sort == 'author_desc' ? 'selected' : '' }}>Author (Z-A)</option>
          <option value="rating_asc" {{ $sort == 'rating_asc' ? 'selected' : '' }}>Rating (Low-High)</option>
          <option value="rating_desc" {{ $sort == 'rating_desc' ? 'selected' : '' }}>Rating (High-Low)</option>
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
              <i class='bx bx-book-reader'></i> Read
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
      @include('components.book-modal', ['book' => $book, 'showAdminActions' => true])
    @endforeach
  </div>

  <!-- Edit Modal -->
  <div id="editModal" class="edit-book-modal">
    <div class="edit-book-modal-content">
      <div class="edit-book-modal-header">
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
        <select id="category_id" name="category_id" required>
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
        <div class="edit-book-modal-footer">
          <button type="button" class="edit-book-btn-secondary" onclick="closeEditModal()">CANCEL</button>
          <button type="submit" class="edit-book-btn-primary">SAVE</button>
        </div>
      </form>
    </div>
  </div>

  <!-- Delete Confirmation Modal -->
  <div id="deleteModal" class="delete-confirmation-modal">
    <div class="delete-confirmation-content">
      <div class="delete-confirmation-header">
        <h2>Delete Book</h2>
      </div>
      <div class="delete-confirmation-body">
        <p>Are you sure you want to delete <span id="deleteBookDetails"></span></p>
        <p class="delete-confirmation-text">This action cannot be undone.</p>
      </div>
      <div class="delete-confirmation-footer">
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
        const modal = document.getElementById('editModal');
        modal.style.display = 'block';
        modal.classList.remove('closing');
        document.body.style.overflow = 'hidden';
      })
      .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while loading the book details');
      });
  }

  function closeEditModal() {
    const modal = document.getElementById('editModal');
    modal.classList.add('closing');
    setTimeout(() => {
      modal.style.display = 'none';
      modal.classList.remove('closing');
      document.body.style.overflow = '';
    }, 300); // Match animation duration
  }

  function confirmDelete(title, author, id) {
    currentDeleteForm = document.getElementById('deleteForm' + id);
    document.getElementById('deleteBookDetails').textContent = `${title} by ${author}`;
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'block';
    modal.classList.remove('closing');
    document.body.style.overflow = 'hidden';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('closing');
    setTimeout(() => {
      modal.style.display = 'none';
      modal.classList.remove('closing');
      document.body.style.overflow = '';
      currentDeleteForm = null;
    }, 300); // Match animation duration
  }

  function submitDelete() {
    if (currentDeleteForm) {
      currentDeleteForm.submit();
    }
  }

  // Close modal when clicking outside
  window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    if (event.target == editModal) {
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
  // Handle dropdown with event delegation
  document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.querySelector('.dropdown-btn');
    const dropdownContent = document.querySelector('.dropdown-content');
    const selectedGenres = new Set();
    let tempSelectedGenres = new Set();

    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
      if (!e.target.closest('.genre-dropdown')) {
        dropdownContent.classList.remove('show');
        // Reset temp selections if not applied
        tempSelectedGenres = new Set(selectedGenres);
        updateCheckboxes();
      }
    });

    // Toggle dropdown
    dropdownBtn.addEventListener('click', function(e) {
      e.stopPropagation();
      dropdownContent.classList.toggle('show');
    });

    // Handle genre selection
    dropdownContent.addEventListener('click', function(e) {
      const li = e.target.closest('li');
      if (li) {
        const checkbox = li.querySelector('input[type="checkbox"]');
        checkbox.checked = !checkbox.checked;

        if (checkbox.checked) {
          tempSelectedGenres.add(checkbox.value);
        } else {
          tempSelectedGenres.delete(checkbox.value);
        }

        updateDropdownButton(tempSelectedGenres);
      }

      // Handle Apply button click
      if (e.target.classList.contains('apply-filters')) {
        selectedGenres.clear();
        tempSelectedGenres.forEach(genre => selectedGenres.add(genre));
        applyFilters();
        dropdownContent.classList.remove('show');
      }

      // Handle Clear button click
      if (e.target.classList.contains('clear-filters')) {
        tempSelectedGenres.clear();
        updateCheckboxes();
        updateDropdownButton(tempSelectedGenres);
      }
    });

    // Fetch and populate genres
    fetch('/get-genres')
      .then(response => response.json())
      .then(genres => {
        const dropdownContent = document.querySelector('.dropdown-content');
        const fragment = document.createDocumentFragment();

        // Get current genres from URL
        const urlParams = new URLSearchParams(window.location.href);
        const currentGenres = urlParams.get('genres') ? urlParams.get('genres').split(',') : [];

        genres.forEach(genre => {
          if (genre) {
            const li = document.createElement('li');
            li.innerHTML = `
              <label class="genre-checkbox-container">
                <input type="checkbox" value="${genre}" ${currentGenres.includes(genre) ? 'checked' : ''}>
                <span class="custom-checkbox"></span>
                <span class="genre-name">${genre}</span>
              </label>
            `;
            fragment.appendChild(li);

            // Add to selected genres if checked
            if (currentGenres.includes(genre)) {
              selectedGenres.add(genre);
              tempSelectedGenres.add(genre);
            }
          }
        });

        // Insert genres before the footer
        const footer = dropdownContent.querySelector('.dropdown-footer');
        dropdownContent.insertBefore(fragment, footer);
        updateDropdownButton(selectedGenres);
      });

    function updateCheckboxes() {
      const checkboxes = document.querySelectorAll('.dropdown-content input[type="checkbox"]');
      checkboxes.forEach(checkbox => {
        checkbox.checked = tempSelectedGenres.has(checkbox.value);
      });
    }

    function applyFilters() {
      const currentUrl = new URL(window.location.href);
      if (selectedGenres.size > 0) {
        currentUrl.searchParams.set('genres', Array.from(selectedGenres).join(','));
      } else {
        currentUrl.searchParams.delete('genres');
      }
      currentUrl.searchParams.delete('page'); // Reset to first page when filtering
      window.location.href = currentUrl.toString();
    }

    function updateDropdownButton(genres) {
      const btn = document.querySelector('.dropdown-btn');
      if (genres.size === 0) {
        btn.textContent = 'Filter by Genres';
      } else if (genres.size === 1) {
        btn.textContent = Array.from(genres)[0];
      } else {
        btn.textContent = `${genres.size} Genres Selected`;
      }
      btn.innerHTML += '<span style="margin-left: auto;">▼</span>';
    }
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
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'block';
    modal.classList.remove('closing');
    document.body.style.overflow = 'hidden';
  }

  function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.classList.add('closing');
    setTimeout(() => {
      modal.style.display = 'none';
      modal.classList.remove('closing');
      document.body.style.overflow = '';
      currentDeleteForm = null;
    }, 300); // Match animation duration
  }

  function submitDelete() {
    if (currentDeleteForm) {
      currentDeleteForm.submit();
    }
  }

  // Close modal when clicking outside
  window.onclick = function(event) {
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');
    if (event.target == editModal) {
      closeEditModal();
    } else if (event.target == deleteModal) {
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
      modal.classList.add('closing');
      setTimeout(() => {
        modal.classList.remove('active');
        modal.classList.remove('closing');
        document.body.style.overflow = '';
      }, 300); // Match animation duration
    });
  }
</script>

<script>
  function applySorting() {
    const sort = document.getElementById('sortSelect').value;
    const currentUrl = new URL(window.location.href);
    currentUrl.searchParams.set('sort', sort);
    currentUrl.searchParams.delete('page'); // Reset to first page when sorting
    window.location.href = currentUrl.toString();
  }
</script>

<script>
  document.addEventListener('DOMContentLoaded', function() {
    // Mobile filter panel functionality
    const filterBtn = document.querySelector('.mobile-filter-btn');
    const filterPanel = document.querySelector('.mobile-filter-panel');
    const filterClose = document.querySelector('.mobile-filter-close');
    const mobileSortSelect = document.getElementById('mobileSortSelect');
    const mobileVisibilityFilter = document.getElementById('mobileVisibilityFilter');
    const mobileDropdownBtn = filterPanel.querySelector('.dropdown-btn');
    const mobileDropdownContent = filterPanel.querySelector('.dropdown-content');
    const selectedGenres = new Set();

    if (filterBtn && filterPanel && filterClose) {
      filterBtn.addEventListener('click', () => {
        filterPanel.classList.add('active');
        document.body.style.overflow = 'hidden';
      });

      filterClose.addEventListener('click', () => {
        filterPanel.classList.remove('active');
        document.body.style.overflow = '';
      });

      filterPanel.addEventListener('click', (e) => {
        if (e.target === filterPanel) {
          filterPanel.classList.remove('active');
          document.body.style.overflow = '';
        }
      });

      // Handle mobile genre dropdown
      if (mobileDropdownBtn) {
        mobileDropdownBtn.addEventListener('click', (e) => {
          e.stopPropagation();
          mobileDropdownContent.classList.toggle('show');
        });
      }

      // Handle genre selection
      if (mobileDropdownContent) {
        mobileDropdownContent.addEventListener('change', function(e) {
          if (e.target.type === 'checkbox') {
            if (e.target.checked) {
              selectedGenres.add(e.target.value);
            } else {
              selectedGenres.delete(e.target.value);
            }
            updateDropdownButton();
            updateResults();
          }
        });
      }

      // Handle mobile sort selection
      if (mobileSortSelect) {
        mobileSortSelect.addEventListener('change', function() {
          const desktopSort = document.getElementById('sortSelect');
          if (desktopSort) {
            desktopSort.value = this.value;
            applySorting();
          }
        });
      }

      // Handle mobile visibility filter
      if (mobileVisibilityFilter) {
        mobileVisibilityFilter.addEventListener('change', function() {
          const desktopVisibility = document.getElementById('visibilityFilter');
          if (desktopVisibility) {
            desktopVisibility.value = this.value;
            applyVisibilityFilter();
          }
        });
      }

      // Sync desktop visibility with mobile
      const desktopVisibility = document.getElementById('visibilityFilter');
      if (desktopVisibility && mobileVisibilityFilter) {
        desktopVisibility.addEventListener('change', function() {
          mobileVisibilityFilter.value = this.value;
        });
      }

      // Close dropdown when clicking outside
      document.addEventListener('click', function(e) {
        if (!e.target.closest('.genre-dropdown')) {
          mobileDropdownContent.classList.remove('show');
        }
      });

      // Prevent panel close when clicking inside
      filterPanel.querySelector('.mobile-filter-content').addEventListener('click', (e) => {
        e.stopPropagation();
      });
    }

    function updateDropdownButton() {
      const btn = mobileDropdownBtn;
      if (selectedGenres.size === 0) {
        btn.textContent = 'Filter by Genres';
      } else if (selectedGenres.size === 1) {
        btn.textContent = Array.from(selectedGenres)[0];
      } else {
        btn.textContent = `${selectedGenres.size} Genres Selected`;
      }
      btn.innerHTML += '<span style="margin-left: auto;">▼</span>';
    }
  });
</script>

</body>

</html>
