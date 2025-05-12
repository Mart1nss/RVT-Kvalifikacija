<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Support Ticket</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container">
    <div class="form-header">
      <h1>Create Ticket</h1>
    </div>

    @if ($errors->any())
      <div class="error-container">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="form-container">
      <form method="POST" action="{{ route('tickets.store') }}">
        @csrf
        <div class="form-group">
          <label for="subject">Subject</label>
          <div class="input-container">
            <input type="text" id="subject" name="subject" required maxlength="50"
              placeholder="Brief description of your issue" oninput="updateCharCount('subject', 'subjectCount', 50)">
            <div class="char-count" id="subjectCount">0 / 50</div>
          </div>
        </div>

        <div class="form-group">
          <label for="category">Category</label>
          <select id="category" name="category" required>
            <option value="">Select Category</option>
            <option value="Technical Issue">Technical Issue</option>
            <option value="Account">Account</option>
            <option value="Feature Request">Feature Request</option>
            <option value="Other">Other</option>
          </select>
        </div>

        <div class="form-group">
          <label for="description">Description</label>
          <div class="input-container">
            <textarea id="description" name="description" rows="6" required maxlength="500"
              placeholder="Please provide detailed information about your issue..."
              oninput="updateCharCount('description', 'descriptionCount', 500)"></textarea>
            <div class="char-count" id="descriptionCount">0 / 500</div>
          </div>
        </div>

        <div class="form-actions">
          <button onclick="window.location.href = '{{ route('tickets.index') }}'"
            class="btn btn-ghost btn-md">Cancel</button>
          <button type="submit" class="btn btn-primary btn-md">Create Ticket</button>
        </div>
      </form>
    </div>
  </div>

  <style>
    .main-container {
      padding: 0 10px;
      max-width: 1000px;
    }

    .form-header {
      margin-bottom: 2rem;
    }

    .form-header h1 {
      color: white;
      text-transform: uppercase;
      font-weight: 800;
      font-size: 32px;
      margin: 0;
    }

    @media (max-width: 768px) {
      .form-header h1 {
        font-size: 28px;
      }
    }

    .error-container {
      background-color: rgb(126, 6, 6);
      padding: 1rem;
      border-radius: 8px;
      margin-bottom: 2rem;
    }

    .error-container ul {
      margin: 0;
      padding-left: 1.5rem;
    }

    .error-container li {
      color: white;
    }

    .form-container {
      background-color: #191919;
      padding: 2rem;
      border-radius: 8px;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      margin-bottom: 0.5rem;
      font-weight: bold;
      color: white;
      text-transform: uppercase;
      font-size: 0.9rem;
    }

    .input-container {
      position: relative;
      width: 100%;
    }

    .char-count {
      position: absolute;
      right: 10px;
      top: 12px;
      background-color: transparent;
      padding: 2px 8px;
      font-size: 12px;
      color: #333;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      width: 100%;
      padding: 0.75rem;
      padding-right: 80px;
      background-color: #252525;
      border: none;
      border-radius: 8px;
      color: white;
      font-size: 1rem;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: white 1px solid;
      background-color: rgb(36, 36, 36);
      border-color: white;
    }

    .form-group select {
      cursor: pointer;
    }

    .form-group select option {
      background-color: #252525;
      color: white;
    }

    .form-group textarea {
      width: 100%;
      resize: vertical;
      min-height: 120px;
      max-height: 200px;
      display: flex;
    }

    .form-actions {
      display: flex;
      justify-content: flex-end;
      gap: 1rem;
      margin-top: 2rem;
    }

    @media (max-width: 768px) {

      .form-container {
        padding: 1rem;
      }

      .btn-ghost {
        width: 100%;
      }
    }
  </style>

  <script>
    function updateCharCount(inputId, counterId, limit) {
      const input = document.getElementById(inputId);
      const counter = document.getElementById(counterId);
      const currentLength = input.value.length;
      counter.textContent = `${currentLength} / ${limit}`;

      if (currentLength >= limit) {
        counter.style.color = '#dc3545';
      } else {
        counter.style.color = '#aaa';
      }
    }

    // Initialize counters
    document.addEventListener('DOMContentLoaded', function() {
      updateCharCount('subject', 'subjectCount', 50);
      updateCharCount('description', 'descriptionCount', 500);
    });
  </script>
</body>

</html>
