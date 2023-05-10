@extends('layouts.master')
@section('title', 'My Groups')
@section('page-content-title', 'Group List')
@section('page-path', 'My Groups')

{{-- main content yeild section --}}
@section('main-content')
    <div class="col-12">
        {{-- searchbar container --}}

        <div class="card">
            <div class="card-header">
                <div class="row mt-2">
                    <section class="searchGroupContainer  col-8">
                        <form method="get" action="{{ route('groups.index') }}">
                            @method('get')
                            <input class="" type="text" name="query"
                                placeholder="Search..."style="min-width: 500px;" value="{{ $query }}">
                            <button class="border border-primary bg-primary ml-1" type="submit" style="min-width: 125px">
                                Search
                            </button>
                        </form>
                    </section>
                    <section class="createGroupContainer col-4">
                        <a class="btn btn-primary float-right" data-toggle="modal" data-target=".createGroup"> Create
                            Group
                        </a>
                    </section>
                </div>
            </div>
            <!-- /.card-header -->
            <div class="card-body">

                <div class="tableWrapper">
                    <table id="example1" class="table table-bordered  table-hover">

                        <thead>
                            <tr>
                                <th>Title
                                    <span class="sortingContainer float-right">
                                        <a href="{{ route('groups.index', ['sort' => 'title', 'order' => 'asc']) }}">
                                            <i class="fa fa-angle-up ml-3" id="descendingTitle"></i>
                                        </a>
                                        <a href="{{ route('groups.index', ['sort' => 'title', 'order' => 'desc']) }}">
                                            <i class="fa fa-angle-down ml-1" id="ascendingTitle"></i>
                                        </a>
                                    </span>
                                </th>
                                <th>Discription
                                    <span class="sortingContainer float-right">
                                        <a href="{{ route('groups.index', ['sort' => 'discription', 'order' => 'asc']) }}">
                                            <i class="fa fa-angle-up ml-3" id="descendingDiscription"></i>
                                        </a>
                                        <a href="{{ route('groups.index', ['sort' => 'discription', 'order' => 'desc']) }}">
                                            <i class="fa fa-angle-down ml-1" id="ascendingDiscription"></i>
                                        </a>
                                    </span>
                                </th>
                                <th>
                                    Group Members
                                </th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($groups) > 0)
                                @foreach ($groups as $key => $group)
                                    <tr>
                                        <td>{{ $group->title }}</td>
                                        <td>{{ $group->discription }}</td>
                                        <td style="min-width: 140px;">{{ $group->total_members }}</td>
                                        <td id="actions">
                                            {{-- edit group --}}
                                            <a class="mr-2 btn text-primary edit-group-link" data-toggle="tooltip"
                                                title="Edit Group" data-id="{{ $group->id }}">
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
                            @else
                                <tr>
                                    <td colspan="4" class="text-center ">
                                        <h2>
                                            No Data Found
                                        </h2>
                                    </td>
                                </tr>
                            @endif
                        </tbody>
                    </table>

                </div>
            </div>

            <div>
                {{ $groups->links() }}
            </div>
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

                    </div>
                    {{-- discription --}}
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Group Discription</label>
                        <textarea class="form-control" id="discriptionCreate" rows="3" name='discription'
                            placeholder="Provide Discription About Group"></textarea>
                    </div>
                    {{-- total users --}}
                    <label for="esxampleFormControlTextarea1">Select Users</label>
                    <select class="js-example-basic-multiple" name="states[]" multiple="multiple"
                        id="allGroupUsersCreate" style="width: 46.75em;">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
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
                <form id="editGroupForm" action="{{ route('groups.update', 8) }}" method="post">
                    @csrf
                    @method('PUT')
                    <input type="hidden" value="" name='groupId' id="hiddenGroupIdEditModal">
                    {{-- name  --}}
                    <div class="form-group">
                        <label for="exampleInputEmail1">Title</label>
                        <input type="text" class="form-control" name="title" id="titleEdit"
                            aria-describedby="nameHelp" placeholder="Provide Name For Group" autofocus>
                    </div>
                    {{-- discription --}}
                    <div class="form-group">
                        <label for="exampleFormControlTextarea1">Group Discription</label>
                        <textarea class="form-control" id="discriptionEdit" rows="3" name='discription'
                            placeholder="Provide Discription About Group"></textarea>
                    </div>
                    {{-- total users --}}
                    <label for="esxampleFormControlTextarea1">Change Users</label>
                    <select class="js-example-basic-multiple" id="selectEdit" name="states[]" multiple="multiple"
                        style="width: 46.75em;">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                        @endforeach
                    </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
            </form>
        </div>
    </div>
</div>


{{-- page script yeild section --}}
@section('page-script')
    <script type="text/javascript">
        $(function() {
            //featching edit group details and appending
            $('.edit-group-link').on('click', function(event) {
                event.preventDefault();
                // alert('clicked');

                //opening model with on click
                $('.edit-group-modal').modal('show')

                //featchingh groupId from Hidden field
                let groupId = $(this).siblings('input[name=groupId]').val();

                //assining it to the value of input field in edit modal in order to featch it directly from request
                $('#hiddenGroupIdEditModal').val(groupId);

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
                // deselecting upper selected group users on close method of modal
                $(".edit-group-modal").on("hidden.bs.modal", function() {
                    // alert('modal closed');
                    $("#selectEdit").each(function() { //added a each loop here
                        $(this).select2('destroy').val("").select2();
                        $('#titleEdit').val('');
                        $('#discriptionEdit').val('');
                    });

                });

            });

            //create form jquery validation
            $("#createGroupForm").validate({
                rules: {
                    title: "required",
                    discription: "required",
                    "states[]": "required"

                },
                messages: {
                    title: "Title field is required.",
                    discription: "discription field is required.",
                    "states[]": "Atleast one user is required."
                },
                // errorPlacement: function(error, element) {
                //     if (element.is(":radio")) {
                //         error.appendTo(element.parents('.form-group'));
                //     } else { // This is the default behavior
                //         error.insertAfter(element);
                //     }
                // },
                submitHandler: function(form) {
                    form.submit();
                }
            });
            //edit form jquery validation
            $("#editGroupForm").validate({
                rules: {
                    title: "required",
                    discription: "required",
                    "states[]": "required"

                },
                messages: {
                    title: "Title field is required.",
                    discription: "discription field is required.",
                    "states[]": "Atleast one user is required."
                },
                // errorPlacement: function(error, element) {
                //     if (element.is(":radio")) {
                //         error.appendTo(element.parents('.form-group'));
                //     } else { // This is the default behavior
                //         error.insertAfter(element);
                //     }
                // },
                submitHandler: function(form) {
                    form.submit();
                }

            });

        })
    </script>

@endsection
