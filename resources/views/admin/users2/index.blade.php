@extends('layouts.template')

@section('title', 'Users (advanced)')

@section('main')
    <h1>Users (advanced)</h1>
    @include('shared.alert')
    <form method="get" action="/admin/users2" id="searchForm">
        <div class="row">
            <div class="col-sm-8 mb-2">
                <a>Filter Name or Email</a>
                <input type="text" class="form-control" name="user" id="user"
                       value="{{ request()->user }}"
                       placeholder="Filter Name Or Email">
            </div>
            <div class="col-sm-4 mb-2">
                <a>Sort by</a>
                <select class="form-control" name="orderBy" id="orderBy">
                    <option value="nameAZ" {{ (request()->orderBy ==  'nameAZ' ? 'selected' : '') }}>Name (A - Z)
                    </option>
                    <option value="nameZA" {{ (request()->orderBy ==  'nameZA' ? 'selected' : '') }}>Name (Z - A)
                    </option>
                    <option value="emailAZ" {{ (request()->orderBy ==  'emailAZ' ? 'selected' : '') }}>Email (A - Z)
                    </option>
                    <option value="emailZA" {{ (request()->orderBy ==  'emailZA' ? 'selected' : '') }}>Email (Z - A)
                    </option>
                    <option value="active" {{ (request()->orderBy ==  'active' ? 'selected' : '') }}>Not Active</option>
                    <option value="admin" {{ (request()->orderBy ==  'admin' ? 'selected' : '') }}>Admin</option>
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
            @foreach($users2 as $user)
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
                    <td data-name="{{$user->name}}" data-id="{{$user->id}}" data-email="{{$user->email}}" data-active="{{$user->active}}" data-admin="{{$user->admin}}">
                        <div class="btn-group btn-group-sm @if(auth()->id() == $user->id) disabled @endif">
                            <button class="btn btn-outline-success btn-edit"
                               data-toggle="tooltip"
                               title="Edit {{ $user->name }}">
                                <i class="fas fa-edit"></i>
                            </button>
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
        {{ $users2->links() }}
    </div>
    @include('admin.users2.modal')
@endsection

@section('script_after')
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

        $(function() {
            $('tbody').on('click', '.btn-delete', function () {
                // Get data attributes from td tag
                let id = $(this).closest('td').data('id');
                let name = $(this).closest('td').data('name')
                // Set some values for Noty
                let text = `<p>Delete the user <b>${name}</b>?</p>`;
                let type = 'warning';
                let btnText = 'Delete user';
                let btnClass = 'btn-success';

                let modal = new Noty({
                    timeout: false,
                    layout: 'center',
                    modal: true,
                    type: type,
                    text: text,
                    buttons: [
                        Noty.button(btnText, `btn ${btnClass}`, function () {
                            // Delete user and close modal
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
                let name = $(this).closest('td').attr('data-name');
                let email = $(this).closest('td').attr('data-email');
                let active = $(this).closest('td').attr('data-active');
                let admin = $(this).closest('td').attr('data-admin');
                // Update the modal
                $('.modal-title').text(`Edit ${name}`);
                $('form').attr('action', `/admin/users2/${id}`);
                $('#id').val(id);
                $('#name').val(name);
                $('input[name="_method"]').val('put');
                $('#email').val(email);
                if (active == 1) $("#active").prop("checked", true);
                else $("#active").prop("checked", false);
                if (admin == 1) $("#admin").prop("checked", true);
                else $("#admin").prop("checked", false);
                // Show the modal
                $('#modal-user2').modal('show');
            });

            $('#modal-user2 form').submit(function (e) {
                // Don't submit the form
                e.preventDefault();
                // Get the action property (the URL to submit)
                let action = $(this).attr('action');
                // Serialize the form and send it as a parameter with the post
                let pars = $(this).serialize();
                console.log(pars);
                // Post the data to the URL
                $.post(action, pars, 'json')
                    .done(function (data) {
                        console.log(data);
                        // Noty success message
                        new Noty({
                            type: data.type,
                            text: data.text
                        }).show();
                        // Hide the modal
                        $('#modal-user2').modal('hide');
                        // Rebuild the table
                        if (data.type != "error") {
                            let id = $("#id").val();
                            let active = "", admin = "";
                            if ($("#active").prop("checked")) active = "<i class=\"fas fa-check\"></i>";
                            if ($("#admin").prop("checked")) admin = "<i class=\"fas fa-check\"></i>";
                            $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(2)').html($("#name").val());
                            $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(3)').html($("#email").val());
                            $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(4)').html(active);
                            $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(5)').html(admin);
                            $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(6)').attr('data-name', $("#name").val());
                            $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(6)').attr('data-email', $("#email").val());
                            if ($("#active").prop("checked")) $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(6)').attr('data-active', '1');
                            else $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(6)').attr('data-active', '0');
                            if ($("#admin").prop("checked")) $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(6)').attr('data-admin', '1');
                            else $("td[data-id='" + id + "']").closest('tr').find('td:nth-child(6)').attr('data-admin', '0');
                        }
                    })
                    .fail(function (e) {
                        console.log('error', e);
                        // e.responseJSON.errors contains an array of all the validation errors
                        console.log('error message', e.responseJSON.errors);
                        // Loop over the e.responseJSON.errors array and create an ul list with all the error messages
                        let msg = '<ul>';
                        $.each(e.responseJSON.errors, function (key, value) {
                            msg += `<li>${value}</li>`;
                        });
                        msg += '</ul>';
                        // Noty the errors
                        new Noty({
                            type: 'error',
                            text: msg
                        }).show();
                    });
            });
        });

        function deleteUser(id) {
            // Delete the genre from the database
            let pars = {
                '_token': '{{ csrf_token() }}',
                '_method': 'delete',
                'id': id
            };
            $.post(`/admin/users2/${id}`, pars, 'json')
                .done(function (data) {
                    console.log('data', data);
                    new Noty({
                        type: data.type,
                        text: data.text
                    }).show();
                    if (data.type != "error") $("td[data-id='" + id + "']").closest('tr').remove();
                })
                .fail(function (e) {
                    console.log('error', e);
                });
        }


    </script>
@endsection
