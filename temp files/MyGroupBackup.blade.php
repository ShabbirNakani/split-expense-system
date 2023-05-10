@extends('layouts.master')
@section('title', 'My Groups')
@section('page-content-title', 'Group List')
@section('page-path', 'My Groups')

{{-- main content yeild section --}}
@section('main-content')

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <a class="btn btn-primary float-right mb-4" data-toggle="modal" data-target=".createGroup"> Create
                    Group
                </a>
                <div class="tableWrapper">
                    <table class="table table-hover">
                        <tr>
                            <th>Sr</th>
                            <th>Title</th>
                            <th>Discription</th>
                            <th>Actions</th>
                        </tr>
                        @foreach ($groups as $key => $group)
                            <tr>
                                <td>{{ ++$key }}</td>
                                <td>{{ $group['title'] }}</td>
                                <td>{{ $group['discription'] }}</td>
                                <td style="display: flex ; ">
                                    {{-- edit group --}}
                                    <a href="{{ route('groups.edit', $group['id']) }}"
                                        class="mr-2 btn text-primary editGroup" data-toggle="modal"
                                        data-target=".edit-group" data-toggle="tooltip" title="Edit Group">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    {{-- show group  --}}
                                    <a href="{{ route('groups.show', $group['id']) }}" class="mr-2 btn text-info"
                                        data-toggle="tooltip" title="Group Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    {{-- delete group --}}
                                    <form action="{{ route('groups.destroy', $group['id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button name="delete" value="delete" class="btn  mr-2  text-danger"
                                            data-toggle="tooltip" title="Delete Group">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>

        <div>
            {{ $groups->links() }}
        </div>
    </div>
@endsection

{{-- craete group modal --}}
<div class="modal fade createGroup" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    id="createGroupModal" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Create a New Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="createGroupForm" action="{{ route('groups.store') }}" method="post">
                    @csrf
                    {{-- name  --}}
                    <div class="form-group">
                        <label for="exampleInputEmail1">Title</label>
                        <input type="text" class="form-control" name="title" id="titleCreate"
                            aria-describedby="nameHelp" placeholder="Provide Name For Group">
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    {{-- discription --}}
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Group Discription</label>
                        <textarea class="form-control" id="discriptionCreate" rows="3" name='discription'
                            placeholder="Provide Discription About Group"></textarea>
                        @error('discription')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    {{-- total users --}}
                    <label for="esxampleFormControlTextarea1">Select Users</label>
                    <select class="js-example-basic-multiple" name="states[]" multiple="multiple"
                        id="allGroupUsersCreate" style="width: 46.75em;" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('states[]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitCreate">Create</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- edit Group modal --}}
<div class="modal fade edit-group  " tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Group</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="editGroupForm" action="{{ route('groups.edit', $groups->id) }}" method="get">
                    @csrf
                    {{-- name  --}}
                    {{-- discription --}}
                    {{-- total users --}}
                    <div class="form-group">
                        <label for="exampleInputEmail1">Title</label>
                        <input type="text" class="form-control" name="title" id="title"
                            aria-describedby="nameHelp" placeholder="Provide Name For Group" value="">
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Group Discription</label>
                        <textarea class="form-control" id="exampleFormControlTextarea1" rows="3" name='discription'
                            placeholder="Provide Discription About Group" value=""></textarea>
                        @error('discription')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <label for="esxampleFormControlTextarea1">Select Users</label>
                    <select class="js-example-basic-multiple" name="states[]" multiple="multiple"
                        style="width: 46.75em;" required>
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
                    @error('states[]')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create</button>
            </div>
            </form>
        </div>
    </div>
</div>


{{-- page script yeild section --}}
@section('page-script')
    <script type="text/javascript">
        $(function() {
            // cereate group ajax call
            let form = $('#createGroupForm');
            form.submit(function(event) {
                event.preventDefault();
                $('#submitCreate').prop('disabled', true);

                $.ajax({
                    type: form.attr('method'),
                    url: form.attr('action'),
                    data: form.serialize(),
                    success: function(data) {
                        console.log('Submission was successful.');
                        console.log(data);
                        $('.table-hover').append(` <tr>
                                <td>{{ ++$key }}</td>
                                <td>  ${data.title}</td>
                                <td>${data.discription}</td>
                                <td style="display: flex ; ">
                                    {{-- edit group --}}
                                    <a href="{{ route('groups.edit', $group['id']) }}"
                                        class="mr-2 btn text-primary editGroup" data-toggle="modal"
                                        data-target=".edit-group" data-toggle="tooltip" title="Edit Group">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    {{-- show group  --}}
                                    <a href="{{ route('groups.show', $group['id']) }}" class="mr-2 btn text-info"
                                        data-toggle="tooltip" title="Group Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    {{-- delete group --}}
                                    <form action="{{ route('groups.destroy', $group['id']) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button name="delete" value="delete" class="btn  mr-2  text-danger"
                                            data-toggle="tooltip" title="Delete Group">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>`);
                        $('#titleCreate').val('');
                        $('#discriptionCreate').val('');
                        $('#allGroupUsersCreate').empty();
                        $('#createGroupModal').modal('toggle');
                        $('#submitCreate').prop('disabled', false);

                    },
                    error: function(data) {
                        console.log('An error occurred.');
                        console.log(data);
                        $('#submitCreate').prop('disabled', false);
                    },
                });
            });

            $('.editGroup').click(function() {
                alert('edit clicked');
            });

        })
    </script>
@endsection
