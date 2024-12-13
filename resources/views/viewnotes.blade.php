<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta http-equiv="X-UA-Compatible" content="ie=edge">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>My Notes</title>
  <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notes-style.css') }}">
  <link rel="stylesheet" href="{{ asset('css/notifications-style.css') }}">
  <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>

<body>

  @include('components.alert')
  @include('navbar')

  <div class="main-container">

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
        <b style="font-size:18px">Book: {{ $note->product->title }}</b> by <b
        style="font-size:18px">{{$note->product->author}} </b><br>
        <span class="last-updated" data-timestamp="{{ $note->updated_at->timestamp }}">
        {{-- dynamic text --}}
        </span>
      </div>

      <div class="note-btn">
        <i class='bx bxs-down-arrow-alt' alt="Expand note"></i>
        <a href="{{ route('view', ['id' => $note->product_id]) }}" class="goto-note-btn" alt="Go to note">
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
      <p style="font-family: sans-serif; font-size: 14px; font-weight: 800; text-transform: uppercase; color: white;">
        You don't have any notes yet.</p>
      </div>
    @endif


    </div>

  </div>


  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.3/jquery.min.js"></script>
  <script>
    $(document).ready(function () {
      $('.div-control').click(function (e) {
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
      $('.sort-button').click(function (e) {
        e.stopPropagation();
        $('.sort-options').toggle();
      });

      // Close dropdown when clicking outside
      $(document).click(function () {
        $('.sort-options').hide();
      });

      // Sort functionality
      $('.sort-options a').click(function (e) {
        e.preventDefault();
        const sortType = $(this).data('sort');

        // Update active state
        $('.sort-options a').removeClass('active');
        $(this).addClass('active');

        // Get all note items
        const $container = $('.item-container');
        const $items = $container.children('.item-card').get();

        // Sort items
        $items.sort(function (a, b) {
          const timeA = $(a).find('.last-updated').data('timestamp');
          const timeB = $(b).find('.last-updated').data('timestamp');

          return sortType === 'newest'
            ? timeB - timeA  // Newest first
            : timeA - timeB; // Oldest first
        });

        // Re-append sorted items
        $.each($items, function (index, item) {
          $container.append(item);
        });

        // Hide dropdown
        $('.sort-options').hide();
      });

      function updateTimeDisplays() {
        $('.last-updated').each(function () {
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