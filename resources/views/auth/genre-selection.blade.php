<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Select Your Interests</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/login-style.css') }}">
  <style>
    .categories-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 1rem;
      margin: 2rem 0;
    }

    .category-card {
      padding: 1rem;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      background-color: #191919;
      color: white;
      text-align: center;
    }

    .category-card.selected {
      background-color: white;
      color: black;
      border: none;
    }

    .error-message {
      color: #e53e3e;
      margin-top: 1rem;
      text-align: center;
    }

    .btn-continue {
      width: 100%;
      height: 48px;
      background: white;
      border: none;
      outline: none;
      border-radius: 8px;
      box-shadow: 2px 2px 8px rgba(0, 0, 0, .1);
      font-size: 12px;
      font-weight: 800;
      cursor: pointer;
      margin-bottom: 1rem;
      display: none;
      transition: all 0.2s;
    }

    .btn-continue:hover {
      opacity: 0.5;
    }

    .btn-skip {
      width: 100%;
      height: 48px;
      background: transparent;
      border: 2px solid white;
      outline: none;
      border-radius: 8px;
      font-size: 12px;
      font-weight: 800;
      cursor: pointer;
      color: white;
      transition: all 0.2s;
    }

    .btn-skip:hover {
      opacity: 0.5;
    }

    .small-text {
      color: white;
      font-weight: 800;
      font-size: 18px;
      font-family: sans-serif;
      text-align: center;
      margin-bottom: 6%;
    }
  </style>
</head>

<body>
  @if (request()->routeIs('preferences.edit'))
    <div class="back-btn-div">
      <span id="back-btn" onclick="window.location.href='{{ '/profile' }}'" class='bx bxs-left-arrow-alt'></span>
    </div>
  @endif

  <div class="login-container" style="max-width: 520px;">
    <h1 class="logo" style="color: white; font-weight: 800; text-transform: uppercase">Welcome
      {{ auth()->user()->name }}!</h1>
    <p class="small-text">Select 3 genres that interest you!</p>

    @include('components.alert')

    <form id="genreForm"
      action="{{ request()->routeIs('preferences.edit') ? route('preferences.store', ['from' => 'edit']) : route('preferences.store') }}"
      method="POST">
      @csrf
      <div class="categories-grid">
        @foreach ($categories as $category)
          <div class="category-card" data-id="{{ $category->id }}" onclick="toggleCategory(this, {{ $category->id }})">
            {{ $category->name }}
          </div>
        @endforeach
      </div>

      <input type="hidden" name="categories" id="selectedCategories"
        value="{{ json_encode($selectedCategories ?? []) }}">

      <div class="error-message" id="errorMessage" style="display: none;">
        Please select exactly 3 categories
      </div>

      <button type="submit" class="btn-continue" id="continueBtn">
        {{ request()->routeIs('preferences.edit') ? 'SAVE' : 'CONTINUE' }}
      </button>
    </form>

    @if (!request()->routeIs('preferences.edit'))
      <form action="{{ route('skip.preferences') }}" method="POST">
        @csrf
        <button type="submit" class="btn-skip">X SKIP</button>
      </form>
    @endif
  </div>

  <script>
    // Get all available category IDs from the DOM
    const availableCategoryIds = Array.from(
      document.querySelectorAll('.category-card')
    ).map(el => parseInt(el.getAttribute('data-id')));
    
    // Filter out any previously selected categories that are no longer available
    let selectedCategories = @json($selectedCategories ?? []).filter(
      categoryId => availableCategoryIds.includes(categoryId)
    );
    
    const maxSelections = 3;

    // Initialize selected categories on page load
    document.addEventListener('DOMContentLoaded', function() {
      selectedCategories.forEach(categoryId => {
        const element = document.querySelector(`.category-card[data-id="${categoryId}"]`);
        if (element) {
          element.classList.add('selected');
        }
      });
      document.getElementById('selectedCategories').value = JSON.stringify(selectedCategories);
      updateUI();
    });

    function toggleCategory(element, categoryId) {
      const index = selectedCategories.indexOf(categoryId);

      if (index === -1) {
        if (selectedCategories.length < maxSelections) {
          selectedCategories.push(categoryId);
          element.classList.add('selected');
        }
      } else {
        selectedCategories.splice(index, 1);
        element.classList.remove('selected');
      }

      document.getElementById('selectedCategories').value = JSON.stringify(selectedCategories);
      updateUI();
    }

    function updateUI() {
      const continueBtn = document.getElementById('continueBtn');
      const errorMessage = document.getElementById('errorMessage');
      const skipBtn = document.querySelector('.btn-skip');

      if (selectedCategories.length === maxSelections) {
        continueBtn.style.display = 'block';
        errorMessage.style.display = 'none';
      } else {
        continueBtn.style.display = 'none';
        errorMessage.style.display = selectedCategories.length > maxSelections ? 'block' : 'none';
      }

      // Hide skip button if editing from profile
      if (skipBtn && window.location.pathname.includes('/preferences/edit')) {
        skipBtn.style.display = 'none';
      }
    }
  </script>
</body>

</html>
