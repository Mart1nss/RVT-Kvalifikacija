<link rel="stylesheet" href="{{ asset('css/book-manage/upload-div.css') }}">

<div class="upload-div">
  <form class="upload-book-form" action="{{ url('uploadbook') }}" method="post" enctype="multipart/form-data">
    @csrf

    <div class="form-group">
      <label class="form-label" for="titleInput">Title</label>
      <input class="form-control" type="text" name="title" id="titleInput" placeholder="Title" maxlength="100">
      <div style="text-align: right; font-size: 0.8em; color: #ccc;">
        <span id="titleCharCount">0</span>/100
      </div>
      <span class="error-message" id="titleError">
        @error('title')
          {{ $message }}
        @enderror
      </span>
    </div>

    <div class="form-group">
      <label class="form-label" for="authorInput">Author</label>
      <input class="form-control" type="text" name="author" id="authorInput" placeholder="Author" maxlength="50">
      <div style="text-align: right; font-size: 0.8em; color: #ccc;">
        <span id="authorCharCount">0</span>/50
      </div>
      <span class="error-message" id="authorError">
        @error('author')
          {{ $message }}
        @enderror
      </span>
    </div>

    <div class="form-group">
      <label class="form-label" for="categoryInput">Category</label>
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
  document.addEventListener('DOMContentLoaded', function () {
    const titleInput = document.getElementById('titleInput');
    const titleCharCount = document.getElementById('titleCharCount');
    const authorInput = document.getElementById('authorInput');
    const authorCharCount = document.getElementById('authorCharCount');

    titleInput.addEventListener('input', function () {
      const currentLength = this.value.length;
      titleCharCount.textContent = currentLength;
      if (currentLength >= 100) {
        titleCharCount.parentElement.style.color = '#dc2626';
      } else {
        titleCharCount.parentElement.style.color = '#ccc';
      }
    });

    authorInput.addEventListener('input', function () {
      const currentLength = this.value.length;
      authorCharCount.textContent = currentLength;
      if (currentLength >= 50) {
        authorCharCount.parentElement.style.color = '#dc2626';
      } else {
        authorCharCount.parentElement.style.color = '#ccc';
      }
    });
  });
</script>
