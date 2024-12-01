<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Page Title</title>
<link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
<link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
<script type="module" src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf.min.mjs"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/4.2.67/pdf_viewer.min.css" integrity="sha512-kQO2X6Ls8Fs1i/pPQaRWkT40U/SELsldCgg4njL8zT0q4AfABNuS+xuy+69PFT21dow9T6OiJF43jan67GX+Kw==" crossorigin="anonymous" referrerpolicy="no-referrer" />

<style>
.pdf-grid {
    background-color: rgb(37, 37, 37);
      border-bottom-left-radius: 10px;
      border-bottom-right-radius: 10px;
      display: grid;        
      gap: 20px;     
      padding: 16px;
      grid-template-columns: repeat(auto-fit, minmax(calc(250px - 60px), 1fr));
}

.pdf-item {
    background-color: #1c1a1a;
      color: white;
      border-radius: 10px;
      border: white 1px solid;
      border-radius: 10px;
      overflow: hidden;
      position: relative;
}

.pdf-item img {
  max-width: 100%; 
  height: auto;  
  width: 100%; 
}

.favorite-btn {
      position: absolute;
      background-color: rgb(37, 37, 37);
      color: white;
      border: none;
      top: 0;
      right: 0;
      margin-right: 10px;
      margin-top: 10px;
      padding: 5px;
      height: 40px;
      width: 50px;
      border-radius: 20px;
      font-weight: 800;
      margin-left: 20px;
      font-size: 16px;
      text-transform: uppercase;
      transition: all 0.15;
    }

    .favorite-btn:hover {
      background-color: rgb(56, 56, 56);
      cursor: pointer;
    }

    .button-container {
      display: flex;
      bottom: 0;
      left: 0;
      margin-bottom: 10px;
      margin-left: 10px;
  }

.info-container {
  display: none; 
  position: absolute;
  bottom: 0;
  left: 0;
  width: 100%;
  padding: 10px;
  background-color: rgba(0, 0, 0, 0.8); 
  border-radius: 0 0 10px 10px; 
}

.item-card:hover .info-container {
  display: block; 
}
</style>
</head>
<body>

    @include('navbar')


    <div class="main-container">

        <div class="text-container">
            <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">All Books</h1>
          </div>

<div class="pdf-grid">

  <div class="pdf-item">
    <img src="https://placehold.co/200x300/EEE/31343C" alt="PDF 1">
    <div class="info-container">
        <div class="button-container" style="display: flex; justify-content: space-between;">
            <a class="view-btn" href="#">View</a>
            <a class="download-btn" href="#">Download</a>
        </div>
    </div>
    <form action="#" method="POST">
        @csrf
        <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
      </form>
  </div>

  <div class="pdf-item">
    <img src="https://placehold.co/200x300/EEE/31343C" alt="PDF 1">
    <div class="info-container">
        <div class="button-container" style="display: flex; justify-content: space-between;">
            <a class="view-btn" href="#">View</a>
            <a class="download-btn" href="#">Download</a>
        </div>
    </div>
    <form action="#" method="POST">
        @csrf
        <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
      </form>
  </div>
  <div class="pdf-item">
    <img src="https://placehold.co/200x300/EEE/31343C" alt="PDF 1">
    <div class="info-container">
        <div class="button-container" style="display: flex; justify-content: space-between;">
            <a class="view-btn" href="#">View</a>
            <a class="download-btn" href="#">Download</a>
        </div>
    </div>
    <form action="#" method="POST">
        @csrf
        <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
      </form>
  </div>
  <div class="pdf-item">
    <img src="https://placehold.co/200x300/EEE/31343C" alt="PDF 1">
    <div class="info-container">
        <div class="button-container" style="display: flex; justify-content: space-between;">
            <a class="view-btn" href="#">View</a>
            <a class="download-btn" href="#">Download</a>
        </div>
    </div>
    <form action="#" method="POST">
        @csrf
        <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
      </form>
  </div>
  <div class="pdf-item">
    <img src="https://placehold.co/200x300/EEE/31343C" alt="PDF 1">
    <div class="info-container">
        <div class="button-container" style="display: flex; justify-content: space-between;">
            <a class="view-btn" href="#">View</a>
            <a class="download-btn" href="#">Download</a>
        </div>
    </div>
    <form action="#" method="POST">
        @csrf
        <button type="submit" class="favorite-btn"><i class='bx bx-star'></i></button>
      </form>
  </div>

 </div>

</div>


</body>
</html>