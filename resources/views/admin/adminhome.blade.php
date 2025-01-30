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
    <h1 class="text-container-title">Admin Dashboard</h1>
  </div>

  <div class="item-container">

    <div class="btn-div">

      <h1 class="welcome-message">
        Welcome, {{ auth()->user()->name }}!</h1>

      <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{ '/uploadpage' }}"><i id="dashboardIcon"
          class='bx bx-cog'></i> Manage Books</a>

      <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{ route('categories.index') }}"><i id="dashboardIcon"
          class='bx bx-category'></i> Manage Categories</a>

      <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{ '/managepage' }}"><i id="dashboardIcon"
          class='bx bx-user'></i> Manage Users</a>

      <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{ '/notifications' }}"><i id="dashboardIcon"
          class='bx bx-bell'></i> notifications</a>

      <a style="margin-bottom: 20px;" class="btn-dashboard" href="{{ url('/audit-logs') }}"><i id="dashboardIcon"
          class='bx bx-history'></i> Audit Logs</a>

      <a class="btn-dashboard" href="{{ '/bookpage' }}"><i id="dashboardIcon" class='bx bx-book'></i> Library</a>

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

  @include('components.book-carousels')

</div>

<script type="module">
  import * as pdfjsLib from 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs';

  pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.worker.min.mjs';

  async function generateThumbnail(pdfPath) {
    try {
      const loadingTask = pdfjsLib.getDocument(pdfPath);
      const pdf = await loadingTask.promise;
      const page = await pdf.getPage(1);

      const viewport = page.getViewport({
        scale: 1
      });
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

      // Get all thumbnail divs with this PDF path
      const thumbnailDivs = document.querySelectorAll('.thumbnail[data-pdfpath="' + pdfPath + '"]');
      thumbnailDivs.forEach(div => {
        div.innerHTML = '';
        div.appendChild(thumbnailImg.cloneNode(true));
      });
    } catch (error) {
      console.error("Error loading PDF:", error);
      // Show fallback image on all instances
      const thumbnailDivs = document.querySelectorAll('.thumbnail[data-pdfpath="' + pdfPath + '"]');
      thumbnailDivs.forEach(div => {
        div.innerHTML =
          '<img src="{{ asset('images/pdf-icon.png') }}" style="width: 100%; height: 100%; object-fit: contain; padding: 20px;">';
      });
    }
  }

  // Process all thumbnails when DOM is loaded
  document.addEventListener('DOMContentLoaded', function() {
    // Create a Set to store unique PDF paths
    const uniquePdfPaths = new Set();

    // Collect all unique PDF paths
    document.querySelectorAll('.thumbnail[data-pdfpath]').forEach(function(thumbnailDiv) {
      uniquePdfPaths.add(thumbnailDiv.dataset.pdfpath);
    });

    // Generate thumbnails for each unique PDF path
    uniquePdfPaths.forEach(function(pdfPath) {
      generateThumbnail(pdfPath);
    });
  });
</script>
