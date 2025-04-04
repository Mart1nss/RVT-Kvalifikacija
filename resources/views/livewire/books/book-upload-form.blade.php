<link rel="stylesheet" href="{{ asset('css/book-manage/upload-div.css') }}">

<div class="upload-div">
  <h2 class="upload-text">Upload Book</h2>

  <form class="upload-book-form" action="{{ url('uploadbook') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <input class="form-control" type="text" name="title" id="titleInput" placeholder="Title">
      <span class="error-message" id="titleError">
        @error('title')
          {{ $message }}
        @enderror
      </span>
    </div>

    <div class="form-group">
      <input class="form-control" type="text" name="author" id="authorInput" placeholder="Author">
      <span class="error-message" id="authorError">
        @error('author')
          {{ $message }}
        @enderror
      </span>
    </div>

    <div class="form-group">
      <select class="form-control" name="category_id" id="categoryInput">
        <option value="">Select Category</option>
        @foreach ($categories as $category)
          <option value="{{ $category->id }}">{{ $category->name }}</option>
        @endforeach
      </select>
      <span class="error-message" id="categoryError">
        @error('category_id')
          {{ $message }}
        @enderror
      </span>
    </div>

    <div class="form-group">
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
      <span class="error-message" id="fileError">
        @error('file')
          {{ $message }}
        @enderror
      </span>
    </div>

    <div class="form-btn">
      <input class="btn-primary" type="submit" value="UPLOAD">
    </div>
  </form>
</div>

<script src="{{ asset('js/book-upload.js') }}"></script>
<script>
  // 1. Upload Form Visibility Toggle
  const uploadFormToggle = document.querySelector(
    '.upload-book-form .visibility-toggle input[type="checkbox"]'
  );
  if (uploadFormToggle) {
    uploadFormToggle.addEventListener("change", function() {
      const label =
        this.closest(".visibility-toggle").querySelector(
          ".visibility-label"
        );
      label.textContent = this.checked ? "Public" : "Private";
    });
  }
</script>
