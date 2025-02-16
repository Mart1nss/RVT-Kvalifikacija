<table id="myTable" class="custom-table">
  <thead>
    <tr>
      <th>ID</th>
      <th>Name</th>
      <th>Email</th>
      <th>User Type</th>
      <th>Last Online</th>
      <th>Created At</th>
      <th>Updated At</th>
      <th>Action</th>
    </tr>
  </thead>
  <tbody>
    @foreach ($users as $user)
      <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
        <td>
          <form action="{{ route('users.updateUserType', $user) }}" method="POST">
            @csrf
            @method('PUT')
            <select name="usertype" onchange="this.form.submit()" class="usertype-select">
              <option value="user" {{ $user->usertype == 'user' ? 'selected' : '' }}>User</option>
              <option value="admin" {{ $user->usertype == 'admin' ? 'selected' : '' }}>Admin</option>
            </select>
          </form>
        </td>
        <td>{{ $user->last_online ? $user->last_online->diffForHumans() : 'Never' }}</td>
        <td>{{ $user->created_at->format('M d, Y') }}</td>
        <td>{{ $user->updated_at->format('M d, Y') }}</td>
        <td>
          <form action="{{ route('users.destroy', $user) }}" method="POST" class="delete-form"
            data-user-id="{{ $user->id }}">
            @csrf
            @method('DELETE')
            <button type="button" class="remove-btn"
              onclick="confirmDelete({{ $user->id }}, '{{ $user->name }}')">
              DELETE
            </button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>

<div class="pagination-container">
  {{ $users->onEachSide(1)->links() }}
