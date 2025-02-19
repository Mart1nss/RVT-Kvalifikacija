<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Forums</title>
  <link rel="stylesheet" href="{{ asset('css/forums.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  @livewireStyles
</head>

<body>
  @include('navbar')

  <div class="main-container">
    <div class="text-container" style="display:flex; justify-content:space-between; align-items:center;">
      <h1 class="text-container-title">Forums</h1>
      <button onclick="window.location.href='{{ route('forums.create') }}'" class="btn btn-primary btn-md">Create
        Forum</button>
    </div>

    <div class="item-container">
      @livewire('forums.forum-list')
    </div>
  </div>

  @livewireScripts

  <script>
    document.addEventListener('livewire:init', () => {
      console.log('Livewire Initialized');

      Livewire.on('searchUpdated', (event) => {
        console.log('Search Updated:', event);
      });
    });
  </script>
</body>

</html>
