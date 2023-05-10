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
                                <td>{{ $group->title }}</td>
                                <td>{{ $group->discription }}</td>
                                <td style="display: flex ; ">
                                    {{-- edit group --}}
                                    <a class="mr-2 btn text-primary edit-group-link" data-toggle="modal"
                                        data-toggle="tooltip" data-target=".edit-group-modal" title="Edit Group"
                                        data-id="{{ $group->id }}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <input type="hidden" value={{ $group->id }} name='groupId'>
                                    {{-- show group  --}}
                                    <a href="{{ route('groups.show', $group->id) }}" class="mr-2 btn text-info"
                                        data-toggle="tooltip" title="Group Details">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    {{-- delete group --}}
                                    <form action="{{ route('groups.destroy', $group->id) }}" method="POST">
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

{{-- create group modal --}}
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

{{-- edit-group-modal --}}
<div class="modal fade edit-group-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel"
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
                <form id="editGroupForm" action="{{ route('groups.update', 2) }}" method="get">
                    @csrf
                    {{-- name  --}}
                    {{-- discription --}}
                    {{-- total users --}}

                    <div class="form-group">
                        <label for="exampleInputEmail1">Title</label>
                        <input type="text" class="form-control" name="title" id="titleEdit"
                            aria-describedby="nameHelp" placeholder="Provide Name For Group" value=""
                            autofocus>
                        @error('title')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Group Discription</label>
                        <textarea class="form-control" id="discriptionEdit" rows="3" name='discription'
                            placeholder="Provide Discription About Group" value=""></textarea>
                        @error('discription')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <label for="esxampleFormControlTextarea1">Change Users</label>
                    <select class="js-example-basic-multiple" id="selectEdit" name="states[]" multiple="multiple"
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
            $('.edit-group-link').on('click', function(event) {
                // event.preventDefault();
                // alert('clicked');
                let groupId = $(this).siblings('input[name=groupId]').val();
                // console.log(groupId);
                var url = "{{ route('groups.edit', ':temp') }}";
                url = url.replace(':temp', groupId);
                $.ajax({
                    url: url,
                    data: groupId,
                    success: function(data) {
                        // console.log('Submission was successful.');
                        console.log('Data', data);
                        // console.log(data.group, 'groups');
                        //name
                        $('#titleEdit').val(data.group.title);
                        //discription
                        $('#discriptionEdit').val(data.group.discription);
                        //users
                        let groupUsers = data.groupUsers;
                        // console.log('groupUsers', groupUsers);

                        //we have an array of object in groupUsers so.....
                        groupUsers.forEach(groupUser => {
                            // console.log(`${groupUser.name}`);

                            //delete those elemets from the list first then append as selected
                            $(`#selectEdit option[value='${groupUser.id}']`).remove();

                            // create the option and append to Select2
                            //note we can not directly select any option using azax so we need to create an new option and append it
                            var option = new Option(groupUser.name, groupUser.id, true,
                                true);
                            $('#selectEdit').append(option).trigger('change');

                            // manually trigger the `select2:select` event
                            $('#selectEdit').trigger({
                                type: 'select2:select',
                                params: {
                                    data: data
                                }
                            });
                        });
                    },
                    error: function(data) {
                        console.log('An error occurred.');
                        console.log(data);
                    },
                });

            });
        })
    </script>
@endsection
