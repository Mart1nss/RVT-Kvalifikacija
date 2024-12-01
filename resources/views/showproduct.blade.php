
<!--

  <!DOCTYPE html>
<html lang="en">1
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <title>Document</title>
</head>

<body>

  <style>
    * {
    padding: 0;
    margin: 0;
    font-family: sans-serif;
    box-sizing: border-box;
  }

  body {
    background-color: rgb(0, 0, 0);
      min-width: 375px;
      align-items: center;
      justify-content: center;
      font-family: sans-serif;

  }

  .container {
    display: flex;
    padding: 10px 100px;
    width: 100%;
  }
  
  .back-btn-div {
      display: flex;
      margin-left: 20px;
      margin-top: 20px;
    }

    #back-btn {
      position: absolute;
      height: 40px;
      width: 40px;
      background: white;
      text-align: center;
      color: black;
      line-height: 40px;
      border-radius: 50%;
      cursor: pointer;
      font-size: 1.25rem;
    }

  </style>


<div class="back-btn-div">
  <span id="back-btn" onclick="window.location.href='{{'/home'}}'" class='bx bxs-left-arrow-alt'></span>
</div>

  <div class="container">
    <h1 style="color: white; text-transform: uppercase; font-weight: 800;">Books</h1>
  <div style="display: flex; flex-wrap: wrap; padding: 5px;">

    @foreach ($data as $data)
      <div style="display: flex; flex-direction: column; margin: 0 20px; width: 200px; height: 200px; border: rgb(255, 255, 255) 1px solid; padding: 5px; box-sizing: border-box;">
        <h5 style="margin: 0; font-size: 1.2em;  color: rgb(255, 255, 255);">{{$data->title ?? ''}}</h5>
        <h5 style="font-size: 1.2em;  color: rgb(255, 255, 255);">{{$data->author ?? ''}}</h5>
        <div style="display: flex; justify-content: space-between;">
          <a href="{{route('view', $data->id)}}">View</a>
          <a href="{{route('download', $data->file)}}">Download</a>
        </div>
      </div>
      @endforeach
  </div>
</div>
</body>
</html>
-->