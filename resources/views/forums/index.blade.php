<html>

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Forums</title>
  <link rel="stylesheet" href="{{ asset('css/components/buttons.css') }}">
  <link rel="stylesheet" href="{{ asset('css/forums/index.css') }}">
</head>

<body>

  @include('navbar')

  <div class="main-container">
    <div class="text-container">
      <h1>Forums</h1>
    </div>
  </div>


</body>

</html>
