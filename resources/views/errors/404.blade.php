<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <title>404</title>

  <style>
    * {
      box-sizing: border-box
    }

    body {
      min-width: 375px;
      align-items: center;
      justify-content: center;
      font-family: sans-serif;
      background-color: rgb(13, 13, 13);
      color: white;
      text-transform: uppercase;
      font-weight: 800;
      font-size: 14px;
      margin: 0;
      padding: 0;
    }

    .main-container {
      max-width: 600px;
      margin: 10% auto;
      padding: 50px;
      justify-content: center;
      align-items: center;

      @media(max-width: 640px) {
        padding: 50px 5px;
        justify-content: center;
        align-items: center;
      }
    }

    .item-container {
      background-color: rgb(13, 13, 13);
      padding: 20px;
      border-radius: 10px;
      margin-bottom: 20px;
      text-align: center;
    }

    .back-btn {
      background-color: white;
      color: black;
      padding: 10px 20px;
      border-radius: 8px;
      text-decoration: none;
      text-align: center;
      display: inline-block;
      margin-top: 20px;
      transition: all 0.2s;
    }

    .back-btn:hover {
      opacity: 0.5;
      cursor: pointer;
    }
  </style>
  <meta name="robots" content="noindex, follow">
</head>

<body>
  <div class="main-container">
    <div class="item-container">
      <h3>Oops! Page not found</h3>
      <h1 style="font-size: 100px; margin: 10px;">404</h1>
      <h2>we are sorry, but the page you requested was not found</h2>
      <a class="back-btn" href="/home">back to homepage</a>
    </div>
  </div>
</body>

</html>
