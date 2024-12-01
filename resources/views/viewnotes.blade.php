
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Notes</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
</head>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>


  .btn-primary {
    margin-top: 20px;
    border: 1px solid white;
    background-color: white;
    height: 38px;
    width: 100%;
    border-radius: 20px;
    font-weight: 800;
    cursor: pointer;
    font-size: 12px;
    text-transform: uppercase;
    transition: all 0.15;
  }
  
  .login-btn {
    border: 1px solid white;
    background-color: black;
    color: white;
    padding: 10px;
    height: 40px;
    width: 120px;
    border-radius: 20px;
    font-weight: 800;
    margin-left: 20px;
    font-size: 12px;
    text-transform: uppercase;
    transition: all 0.15;
  }

  
  .remove-btn {
    position: absolute;
    top: 0;
    right: 0;
    color: rgb(255, 0, 0);
    text-decoration: none;
    border: rgb(255, 0, 0) 1px solid;
    border-radius: 20px;
    margin-right: 5px;
    margin-top: 10px;
    padding: 5px;
    font-family: sans-serif;
    font-weight: 800;
    font-size: 20px;
    text-transform: uppercase;
    background-color: #1a1a1a;
    cursor: pointer;
    width: 40px;
  }
  
  .view-btn {
    border: 1px solid rgb(0, 0, 0);
    background-color: rgb(255, 255, 255);
    color: rgb(0, 0, 0);
    padding: 10px;
    border-radius: 20px;
    font-weight: 800;
    font-size: 12px;
    text-transform: uppercase;
    text-decoration: none;
  }

  .favorites-card {
    background-color: rgb(37, 37, 37);
    color: white;

  }

  .item-container {
    background-color: rgb(37, 37, 37);
    border-bottom-left-radius: 10px;
    border-bottom-right-radius: 10px;
    display: grid;
    grid-gap: 16px;
    padding: 16px;
}

.item-card {
      background-color: #1c1a1a;
      color: white;
      padding: 10px;
      border-radius: 10px;
      border: white 1px solid;
      border-radius: 10px;
      height: min-content;
      position: relative;
      width: 100%;
      vertical-align: middle;

  }

  .note-content {
    background-color: rgb(37, 37, 37); 
    border: 1px solid white;    
    border-top: none;           
    border-bottom-left-radius: 8px;  
    border-bottom-right-radius: 8px; 
    padding: 10px;          
    top: 100%;                  
    left: 0;                    
    width: 100%;               
    box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.2); 
    z-index: -1; 
  }

  .div-control {
    display: flex;
    justify-content: space-between;
    cursor: pointer;
  }

  #dropdown-btn {
    float: right;
    height: 40px;
    width: 40px;
    background: white;
    text-align: center;
    color: black;
    line-height: 40px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 20px;
    margin-left: 20px;
  }

  @media (max-width: 769px) {
    #dropdown-btn {
      margin-left: 10px;
    }
  }

.note-header { cursor: pointer; } 

.last-updated em { 
    color: gray;   
    font-style: italic; 
}

.badge {
  background-color: red;
  border-radius: 20px;
  width: 20px;
  position: absolute;
  top: 15;
}

.note-btn {
  width: 40%;
}

.goto-note-btn {
    float: right;
    height: 40px;
    width: 40px;
    background: white;
    text-align: center;
    color: black;
    font-size: 20px;
    border-radius: 50%;
    cursor: pointer;
}

.goto-note-btn i {
  padding-top: 8px;
}

#dropdown-4 {
  background-color: rgb(56, 56, 56);
}

</style>

</head>
<body>

  @include('navbar')

  <div class="main-container" >

    @if ($errors->any())
    <div class="alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('success'))
    <div class="alert-success">
        {{ session('success') }}
    </div>
@endif

    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Notes</h1>
    </div>

    <div class="item-container">

      @if($notes->count() > 0)

      @foreach ($notes as $note)

      <div class="item-card">
 
        <div class="div-control">
            <div class="note-header" data-note-id="{{ $note->id }}">
                <b style="font-size:18px">Book: {{ $note->product->title }}</b> by <b style="font-size:18px">{{$note->product->author}} </b><br>
                <span class="last-updated" data-timestamp="{{ $note->updated_at->timestamp }}">
                  {{-- dynamic text  --}}
              </span>
            </div>

            <div class="note-btn">
              
              <i id="dropdown-btn" class='bx bxs-down-arrow-alt'></i>

              <a href="{{ route('view', ['id' => $note->product_id]) }}" class="goto-note-btn">
                <i class='bx bxs-arrow-to-right'></i>
              </a>
            </div>
        </div>
        <div class="note-content" style="display: none;">
            {{ $note->note_text }}
        </div>

    </div>

    @endforeach
  @else
      <p style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">You don't have any notes yet.</p>
  @endif


    </div>

  </div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.div-control').click(function() {
                let noteId = $(this).data('note-id');
                $(this).next('.note-content').slideToggle();
            });

function updateTimeDisplays() {
    $('.last-updated').each(function() {
        const timestamp = $(this).data('timestamp') * 1000;
        const timeAgoText = moment(timestamp).fromNow(); 
        $(this).html('Last updated <em>' + timeAgoText + '</em>');
    });
}

      updateTimeDisplays();

      setInterval(updateTimeDisplays, 60000); 
          });
    </script>
  
</body>
</html>