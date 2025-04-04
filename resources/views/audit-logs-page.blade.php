<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="{{ asset('css/audit-logs.css') }}">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Audit Logs</title>
  @livewireStyles
</head>

<body>
  @include('navbar')

  @livewire('audit-logs')

  @livewireScripts

</body>

</html>
