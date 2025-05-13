<!DOCTYPE html>

<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <title>Create Forum</title>
  <link rel="stylesheet" href="{{ asset('css/forums.css') }}">
  <link rel="stylesheet" href="{{ asset('css/main-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  @livewireStyles
</head>

<body>
  @include('navbar')

  <div class="main-container" style="padding: 0 10px; max-width: 1000px;">

    <div class="text-container" style="display:flex; justify-content:space-between; align-items:center;">
      <h1 class="text-container-title">Create New Forum</h1>
    </div>
      @livewire('forums.create-forum')
  </div>

  @livewireScripts

</body>
