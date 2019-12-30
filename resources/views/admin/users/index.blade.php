@extends('layouts.template')

@section('title', 'Users')

@section('main')
    <h1>Users</h1>
    @include('shared.alert')
    <form method="get" action="/admin/users" id="searchForm">
        @csrf
        <div class="row">
            <div class="col-sm-8 mb-2">
                <a>Filter Name or Email</a>
                <input type="text" class="form-control" name="user" id="user"
                       value="{{ request()->user }}" placeholder="Filter Name Or Email">
            </div>
            <div class="col-sm-4 mb-2">
                <a>Sort by</a>
                <select class="form-control" name="orderBy" id="orderBy">
                    <option value="nameQasc" {{ (request()->orderBy ==  'nameQasc' ? 'selected' : '') }}>Name (A ⇒ Z)</option>
                    <option value="nameQdesc" {{ (request()->orderBy ==  'nameQdesc' ? 'selected' : '') }}>Name (Z ⇒ A)</option>
                    <option value="emailQasc" {{ (request()->orderBy ==  'emailQasc' ? 'selected' : '') }}>Email (A ⇒ Z)</option>
                    <option value="emailQdesc" {{ (request()->orderBy ==  'emailQdesc' ? 'selected' : '') }}>Email (Z ⇒ A)</option>
                    <option value="activeQdesc" {{ (request()->orderBy ==  'activeQdesc' ? 'selected' : '') }}>Not Active</option>
                    <option value="adminQdesc" {{ (request()->orderBy ==  'adminQdesc' ? 'selected' : '') }}>Admin</option>
                </select>
            </div>
        </div>
    </form>
    <div class="table-responsive">
        <table class="table">
            <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Email</th>
                <th>Active</th>
                <th>Admin</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->active==1)
                            <i class="fas fa-check"></i>
                        @endif
                    </td>
                    <td>
                        @if($user->admin==1)
                            <i class="fas fa-check"></i>
                        @endif
                    </td>
                    <td data-name="{{$user->name}}" data-id="{{$user->id}}">
                        <div class="btn-group btn-group-sm @if(auth()->id() == $user->id) disabled @endif">
                            <a href="/admin/users/{{ $user->id }}/edit" class="btn btn-outline-success"
                               data-toggle="tooltip"
                               title="Edit {{ $user->name }}">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button type="submit" class="btn btn-outline-danger btn-delete"
                                    data-toggle="tooltip"
                                    title="Delete {{ $user->name }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    {{ $users->links() }}
@endsection

@section('script_after')
    <script>
        $(function () {

            $('tbody').on('click', '.btn-delete', function () {
                // Get data attributes from td tag
                let id = $(this).closest('td').data('id');
                let name = $(this).closest('td').data('name');
                // Set some values for Noty
                let text = `<p>Delete the user <b>${name}</b>?</p>`;
                let type = 'warning';
                let btnText = 'Delete user';
                let btnClass = 'btn-success';
                // Show Noty
                let modal = new Noty({
                    timeout: false,
                    layout: 'center',
                    modal: true,
                    type: type,
                    text: text,
                    buttons: [
                        Noty.button(btnText, `btn ${btnClass}`, function () {
                            // Delete genre and close modal
                            deleteUser(id);
                            modal.close();
                        }),
                        Noty.button('Cancel', 'btn btn-secondary ml-2', function () {
                            modal.close();
                        })
                    ]
                }).show();
            });

            $('tbody').on('click', '.btn-edit', function () {
                // Get data attributes from td tag
                let id = $(this).closest('td').data('id');
                let name = $(this).closest('td').data('name');
                // Update the modal
                $('.modal-title').text(`Edit ${name}`);
                $('form').attr('action', `/admin/genres/${id}`);
                $('#name').val(name);
                $('input[name="_method"]').val('put');
                // Show the modal
                $('#modal-genre').modal('show');
            });

        });

        function deleteUser(id) {
            let pars = {
                '_token': '{{ csrf_token() }}',
                '_method': 'delete'
            };
            $.post(`/admin/users/${id}`, pars, 'json')
                .done(function (data) {
                    console.log('data', data);
                    location.replace('/admin/users');
                })
                .fail(function (e) {
                    console.log('error', e);
                    location.replace("/admin/users");
                });
        }
    </script>
    <script>
        $(function () {
            $('#user').blur(function () {
                $('#searchForm').submit();
            });
            $('#orderBy').change(function () {
                $('#searchForm').submit();
                console.log($("#searchForm"))
            });
        })
    </script>
@endsection
