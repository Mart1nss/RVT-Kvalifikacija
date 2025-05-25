<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Upload New Book</title>
  <link rel="stylesheet" href="{{ asset('css/allbooks-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/book-manage/upload-div.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>

  @livewireStyles
</head>

<body>
  @include('components.alert')
  @include('navbar')

  <div class="main-container" style="padding-top: 20px;  max-width: 1000px;">
    <div class="text-container" style="background-color: transparent; padding-left: 0px;">
      <h1 class="text-container-title">Upload New Book</h1>
    </div>
    <div style="display: flex; justify-content: center;">
        @include('books.upload-form', ['categories' => $categories])
    </div>
  </div>

  <script src="{{ asset('js/book-upload.js') }}"></script>
  @livewireScripts
</body>

</html>
