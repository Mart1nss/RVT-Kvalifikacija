<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Forums</title>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <link rel="stylesheet" href="{{ asset('css/forums.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  @livewireStyles
</head>

<body>
  @include('navbar')
  @include('components.alert')

  <div class="main-container" style="max-width: 1000px;">
    @livewire('forums.forum-view', ['forum' => $forum])
  </div>

  @livewireScripts
</body>

</html>
