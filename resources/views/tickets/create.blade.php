<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Support Ticket</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    @include('components.alert')
    @include('navbar')

    <div class="main-container">
        <div class="form-header">
            <h1>Create New Support Ticket</h1>
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
                    <label for="title">Title</label>
                    <div class="input-container">
                        <input type="text" id="title" name="title" required 
                               maxlength="50"
                               placeholder="Brief description of your issue"
                               oninput="updateCharCount('title', 'titleCount', 50)">
                        <div class="char-count" id="titleCount">0 / 50</div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="category">Category</label>
                    <select id="category" name="category" required>
                        <option value="">Select Category</option>
                        <option value="Technical Issue">Technical Issue</option>
                        <option value="Account">Account</option>
                        <option value="Billing">Billing</option>
                        <option value="Feature Request">Feature Request</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <div class="input-container">
                        <textarea id="description" name="description" rows="6" required
                                maxlength="5000"
                                placeholder="Please provide detailed information about your issue..."
                                oninput="updateCharCount('description', 'descriptionCount', 5000)"></textarea>
                        <div class="char-count" id="descriptionCount">0 / 5000</div>
                    </div>
                </div>

                <div class="form-actions">
                    <a href="{{ route('tickets.index') }}" class="cancel-btn">Cancel</a>
                    <button type="submit" class="submit-btn">Create Ticket</button>
                </div>
            </form>
        </div>
    </div>

    <style>
    body {
        background-color: #1c1a1a;
        color: white;
        font-family: sans-serif;
    }

    .main-container {
        padding: 0;
    }

    .form-header {
        margin-bottom: 2rem;
    }

    .form-header h1 {
        color: white;
        text-transform: uppercase;
        font-weight: 800;
        margin: 0;
    }

    .error-container {
        background-color: #dc3545;
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
        background-color: #2d2d2d;
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
        color: #aaa;
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
        top: 10px;
        background-color: #1c1a1a;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 12px;
        color: #aaa;
    }

    textarea + .char-count {
        top: 10px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 0.75rem;
        padding-right: 80px;
        background-color: #1c1a1a;
        border: 1px solid #3d3d3d;
        border-radius: 4px;
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
        background-color: #1c1a1a;
        color: white;
    }

    .form-group textarea {
        resize: vertical;
        min-height: 120px;
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
    }

    .cancel-btn,
    .submit-btn {
        padding: 0.75rem 1.5rem;
        border-radius: 4px;
        font-weight: bold;
        text-transform: uppercase;
        cursor: pointer;
        font-size: 0.9rem;
    }

    .cancel-btn {
        background-color: #6c757d;
        color: white;
        border: none;
        text-decoration: none;
    }

    .cancel-btn:hover {
        background-color: #5a6268;
    }

    .submit-btn {
        background-color: white;
        color: black;
        border: 1px solid white;
        font-weight: 800;
        transition: all 0.15s;
    }

    .submit-btn:hover {
        opacity: 0.7;
    }

    /* Placeholder styling */
    ::placeholder {
        color: #6c757d;
        opacity: 1;
    }

    :-ms-input-placeholder {
        color: #6c757d;
    }

    ::-ms-input-placeholder {
        color: #6c757d;
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
        updateCharCount('title', 'titleCount', 50);
        updateCharCount('description', 'descriptionCount', 5000);
    });
    </script>
</body>
</html>