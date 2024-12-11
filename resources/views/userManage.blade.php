
<html lang="en" >
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management</title>
    <link rel="stylesheet" href="{{ asset('css/navbar-style.css') }}">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/usermanage-style.css') }}">
</head>
<body>


@include('navbar')



  <div class="main-container" >

    <div class="text-container">
      <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">User Management</h1>
    </div>

  <div class="item-container">
    

    <!-- Filters --> 
    <div class="filter-div"> 
      <input type="text" id="myInput" onkeyup="myFunction()" placeholder="Search for names..." title="Type in a name">

      <button id="sortButton" onclick="toggleSort()">Sort Names A-Z</button>
    </div>

    <div style="overflow-x:auto;">
      <table id="myTable">
        <thead>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>User Type</th>
                <th>Created At</th>
                <th>Updated At</th>
                <!--<th>Edit</th>-->
                <th>Delete</th>
                
            </tr>
        </thead>
        <tbody>
          @foreach($users as $user)
            <tr style="">
              <td>{{ $user->id }}</td>
              <td>{{ $user->name }}</td>
              <td>{{ $user->email }}</td>
              <td>
                <form action="{{ route('users.updateUserType', $user) }}" method="POST">
                  @csrf
                  @method('PUT')
                  <select name="usertype" onchange="this.form.submit()">
                    <option value="user" {{ $user->usertype == 'user' ? 'selected' : '' }}>User</option>
                    <option value="admin" {{ $user->usertype == 'admin' ? 'selected' : '' }}>Admin</option>
                  </select>
                </form>
              </td>
              <td>{{ $user->created_at }}</td>
              <td>{{ $user->updated_at }}</td>
              <!--<td>
                <a class="download-btn" data-id="{{ $user->id }}">Edit</a> 
              </td>-->
              <td>
              <form action="{{ route('users.destroy', $user) }}" method="POST">
                @csrf
                @method('DELETE') 
                <button class="remove-btn" type="submit" onclick="return confirm('Are you sure you want to delete this user?')">Delete</button>
              </form>
              </td>
              
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>


  <div class="text-container">
    <h1 style="color: white; text-transform:uppercase; font-family: sans-serif; font-weight: 800;">Send notification</h1>
  </div>

  <div class="item-container">
    <div class="filter-div">
      <form method="POST" action="{{ route('admin.send.notification') }}">
        @csrf
        <div class="filter-div">
        <textarea class="notif-input" name="message" placeholder="Enter your notification message"></textarea>
        <button class="send-btn" type="submit">Send</button>
      </div>
      </form>

  </div>



  </div>


  


    <script>  

function myFunction() {
    var input, filter, table, tr, td, i, txtValue;
    input = document.getElementById("myInput");
    filter = input.value.toUpperCase();
    table = document.getElementById("myTable");
    tr = table.getElementsByTagName("tr");
    for (i = 0; i < tr.length; i++) {
      td = tr[i].getElementsByTagName("td")[1]; 
      if (td) {
        txtValue = td.textContent || td.innerText;
        if (txtValue.toUpperCase().indexOf(filter) > -1) {
          tr[i].style.display = "";
        } else {
          tr[i].style.display = "none";
        }
      }
    }
  }

  var isSorted = false;

  function filterNamesAtoZ() {
    var table, rows, switching, i, x, y, shouldSwitch;
    table = document.getElementById("myTable");
    switching = true;
    while (switching) {
      switching = false;
      rows = table.rows;
      for (i = 1; i < (rows.length - 1); i++) {
        shouldSwitch = false;
        x = rows[i].getElementsByTagName("TD")[1];
        y = rows[i + 1].getElementsByTagName("TD")[1];
        if (x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) {
          shouldSwitch = true;
          break;
        }
      }
      if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
      }
    }
  }

  function toggleSort() {
    if (!isSorted) {
      filterNamesAtoZ();
      isSorted = true;
      document.getElementById("sortButton").innerHTML = "Clear Sorting";
      document.getElementById("sortButton").style.backgroundColor = "red";
    } else {
      resetSort();
      isSorted = false;
      document.getElementById("sortButton").innerHTML = "Sort Names A-Z";
      document.getElementById("sortButton").style.backgroundColor = "";
    }
  }

  function resetSort() {
    var table, rows, i, switching, shouldSwitch, dir, switchcount = 0;
    table = document.getElementById("myTable");
    switching = true;
    dir = "asc"; 
    while (switching) {
      switching = false;
      rows = table.rows;
      for (i = 1; i < (rows.length - 1); i++) {
        shouldSwitch = false;
        if (dir == "asc") {
          if (rows[i].innerHTML.toLowerCase() > rows[i + 1].innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
          }
        } else if (dir == "desc") {
          if (rows[i].innerHTML.toLowerCase() < rows[i + 1].innerHTML.toLowerCase()) {
            shouldSwitch = true;
            break;
          }
        }
      }
      if (shouldSwitch) {
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        switchcount ++;
      } else {
        if (switchcount == 0 && dir == "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }

  }

      </script>


<script>
  // ... Other JavaScript code...

  editButtons.forEach(button => {
    button.addEventListener('click', (event) => {
      const userId = event.target.dataset.id;
      const user = document.getElementById(`user-${userId}`);
      user.isEditing = true; // Mark the user as being edited
      window.location.href = window.location.href; // Refresh the page
    });
  });

  cancelButtons.forEach(button => {
    button.addEventListener('click', (event) => {
      const userId = event.target.dataset.id;
      const user = document.getElementById(`user-${userId}`);
      user.isEditing = false; // Mark the user as not being edited
      window.location.href = window.location.href; // Refresh the page
    });
  });
</script>
</body>
</html>