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
    border-radius: 10px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-top: 10px;
  }

  .item-card {
    position: relative;
    margin-bottom: 10px;
    z-index: 1;
  }

  .item-card-header {
    background-color: #1c1a1a;
    color: white;
    padding: 10px;
    margin: 0px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
    position: relative;
    z-index: 2;
  }

  .div-control {
    cursor: pointer;
    display: flex;
    justify-content: flex-start;
    align-items: center;
    padding: 10px;
    width: 100%;
  }

  .note-header {
    flex-grow: 1;
    cursor: pointer;
  }

  .note-btn {
    display: flex;
    gap: 15px;
    align-items: center;
    margin-left: auto;
    justify-content: flex-end;
    min-width: max-content;
  }

  .note-btn i,
  .goto-note-btn i {
    font-size: 20px;
    color: black;
    background: white;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    cursor: pointer;
    padding: 0;
  }

  .goto-note-btn {
    text-decoration: none;
    display: flex;
    align-items: center;
  }

  .note-content {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background-color: #1c1a1a;
    color: white;
    margin: 0px 20px; 
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    z-index: 1;
    display: none;
  }

  .last-updated em { 
    color: gray;   
    font-style: italic; 
  }

  .div-control[aria-expanded="true"] .bxs-down-arrow-alt {
    transform: rotate(180deg);
  }

  .badge {
    background-color: red;
    border-radius: 20px;
    width: 20px;
    position: absolute;
    top: 15;
  }

  #dropdown-4 {
    background-color: rgb(56, 56, 56);
  }

  .text-container {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin: 0;
    padding: 10px;
  }

  .text-container h1 {
    color: white;
    text-transform: uppercase;
    font-family: sans-serif;
    font-weight: 800;
    margin: 0;
    padding-left: 6px;
  }

  .sort-dropdown {
    position: relative;
    display: inline-block;
    padding-right: 6px;
  }

  .sort-button {
    background-color: #1c1a1a;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 10px;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 10px;
  }

  .sort-button i {
    font-size: 20px;
  }

  .sort-options {
    display: none;
    position: absolute;
    right: 0;
    background-color: #1c1a1a;
    min-width: 160px;
    border: 1px solid white;
    border-radius: 10px;
    z-index: 1000;
    margin-top: 5px;
  }

  .sort-options a {
    color: white;
    padding: 12px 16px;
    text-decoration: none;
    display: block;
    cursor: pointer;
  }

  .sort-options a:hover {
    background-color: #2a2a2a;
  }

  .sort-options a.active {
    background-color: #2a2a2a;
  }

  .sort-options a:first-child {
    border-radius: 10px 10px 0 0;
  }

  .sort-options a:last-child {
    border-radius: 0 0 10px 10px;
  }

  .main-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
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

    @if($notes->count() > 0)
    <div class="item-container">
      <div class="text-container">
        <h1>Notes</h1>
        <div class="sort-dropdown">
          <button class="sort-button">
            Sort by <i class='bx bx-sort-alt-2'></i>
          </button>
          <div class="sort-options">
            <a href="#" data-sort="newest" class="active">Newest First</a>
            <a href="#" data-sort="oldest">Oldest First</a>
          </div>
        </div>
      </div>

      @foreach ($notes as $note)

      <div class="item-card">
        <div class="item-card-header">
          <div class="div-control" data-note-id="{{ $note->id }}">
              <div class="note-header">
                  <b style="font-size:18px">Book: {{ $note->product->title }}</b> by <b style="font-size:18px">{{$note->product->author}} </b><br>
                  <span class="last-updated" data-timestamp="{{ $note->updated_at->timestamp }}">
                    {{-- dynamic text  --}}
                </span>
              </div>

              <div class="note-btn">
                <i class='bx bxs-down-arrow-alt' alt="Expand note"></i>
                <a href="{{ route('view', ['id' => $note->product_id]) }}" class="goto-note-btn" alt="Go to note"	>
                  <i class='bx bxs-arrow-to-right'></i>
                </a>
              </div>
          </div>
        </div>
        <div class="note-content">
            {{ $note->note_text }}
        </div>

    </div>

    @endforeach
  @else
      <div class="item-container">
        <div class="text-container">
          <h1>Notes</h1>
          <div class="sort-dropdown">
            <button class="sort-button">
              Sort by <i class='bx bx-sort-alt-2'></i>
            </button>
            <div class="sort-options">
              <a href="#" data-sort="newest" class="active">Newest First</a>
              <a href="#" data-sort="oldest">Oldest First</a>
            </div>
          </div>
        </div>
        <p style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">You don't have any notes yet.</p>
      </div>
    @endif


    </div>

  </div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
    <script>
        $(document).ready(function() {
            $('.div-control').click(function(e) {
                // Prevent click from triggering if clicking the goto button
                if ($(e.target).closest('.goto-note-btn').length) {
                    return;
                }
                
                const $control = $(this);
                const $content = $control.closest('.item-card').find('.note-content');
                const $card = $control.closest('.item-card');
                
                // Toggle aria-expanded attribute
                $control.attr('aria-expanded', 
                    $control.attr('aria-expanded') === 'true' ? 'false' : 'true'
                );
                
                // Simple toggle
                if ($content.is(':visible')) {
                    $content.hide();
                    $card.css('margin-bottom', '10px');
                } else {
                    $content.show();
                    $card.css('margin-bottom', $content.outerHeight() + 20 + 'px');
                }
            });

            // Sort dropdown toggle
            $('.sort-button').click(function(e) {
                e.stopPropagation();
                $('.sort-options').toggle();
            });

            // Close dropdown when clicking outside
            $(document).click(function() {
                $('.sort-options').hide();
            });

            // Sort functionality
            $('.sort-options a').click(function(e) {
                e.preventDefault();
                const sortType = $(this).data('sort');
                
                // Update active state
                $('.sort-options a').removeClass('active');
                $(this).addClass('active');

                // Get all note items
                const $container = $('.item-container');
                const $items = $container.children('.item-card').get();

                // Sort items
                $items.sort(function(a, b) {
                    const timeA = $(a).find('.last-updated').data('timestamp');
                    const timeB = $(b).find('.last-updated').data('timestamp');
                    
                    return sortType === 'newest' 
                        ? timeB - timeA  // Newest first
                        : timeA - timeB; // Oldest first
                });

                // Re-append sorted items
                $.each($items, function(index, item) {
                    $container.append(item);
                });

                // Hide dropdown
                $('.sort-options').hide();
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