@extends('layouts.master')
@section('title', 'Group Details')

@section('page-links')
@endsection
{{-- main upper title  --}}
@section('page-content-title')
    {{-- {{ $group->title }} --}}
    Group
@endsection
@section('page-path')
    <a href="{{ route('groups.index') }}">All Groups </a> / {{ $group->title }}
@endsection

{{-- main content --}}
@section('main-content')
    <div class="col-12">
        {{-- group details and members --}}
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-12">
                        <div class="row text-dark">
                            <div class="col-12">
                                <h4>{{ ucWords($group->title) }}</h4>
                                <input type="hidden" name="groupId" value="{{ $group->id }}" id="groupId">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                <h5> {{ ucWords($group->discription) }}</h5>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <h3 class="">
                    Members
                </h3>
                <div class="row">
                    @foreach ($groupUsers as $groupUser)
                        <div class="col-md-2">
                            <h4><span class="badge badge-secondary">{{ ucWords($groupUser->name) }}</span></h4>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
        {{-- search filter and list expenses --}}
        <div class="card">
            <div class="card-body">
                <div class="row mb-4 mt-2">
                    <div class="col-12">
                        {{-- search  live --}}
                        <input class="form-control" type="text" id="search" name="search" placeholder="Search...">
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-8">
                        <div class="row">
                            <div class="col-3">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <label class="input-group-text" for="inputGroupSelect01">Users</label>
                                    </div>
                                    <select class="custom-select" id="filterUser">
                                        <option selected value="null">Choose...</option>
                                        <option value="{{ Auth::user()->id }}">{{ Auth::user()->name }}</option>
                                        @foreach ($groupUsers as $groupUser)
                                            <option value="{{ $groupUser->id }}">{{ $groupUser->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <label for="range" class="mt-1 ml-3 h5"> Dates:</label>
                            <div class="col-3">
                                {{-- date picker --}}
                                <input type="text" class="form-control" name="daterange" id="dateRange" />
                            </div>
                            <div class="col-2">
                                <input class="form-control btn btn-primary" type="button" value="Apply Filter"
                                    name="filterButton" id="filterButton">
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <a class="btn btn-primary float-right" id="add-expense-button" data-toggle="modal"
                            data-target="#create-expense-modal">
                            Add Expense
                        </a>
                    </div>
                </div>
                {{-- table --}}
                <div class="row tableHolder">
                    <table id="expense-table" class="table table-bordered  table-hover">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Members</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody class="tbody">
                            {{-- sample row --}}
                            {{--
                            <tr>
                                <td>title</td>
                                <td>members</td>
                                <td>Amount</td>
                                <td>Date</td>
                                <td id="actions">
                                    edit group
                                    <a class="mr-2 btn text-primary" data-toggle="modal" data-target="#edit-expense-modal"
                                        title="Edit Expense">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                    <input type="hidden" value={{ $group->id }} name='groupId'>
                                    delete group
                                    <button name="delete" value="delete" class="btn  mr-2  text-danger" data-toggle="modal"
                                        data-target="#delete-expense-modal">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                             </tr>
                            --}}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="buttonHOlder">
            <a href="{{ route('groups.index') }}" class="btn btn-primary float-right">Go Back</a>
        </div>
    </div>
@endsection

{{-- create expense modal --}}
<div class="modal fade" id="create-expense-modal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add new expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="createExpenseForm" method="POST">
                    @csrf
                    {{-- title --}}
                    <input type="hidden" name="groupId" value="{{ $group->id }}" id="groupId">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="title" id="titleCreate"
                            aria-describedby="nameHelp" placeholder="Enter Expense Detail">
                    </div>
                    {{-- amount --}}
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" name="amount" id="amountCreate"
                            aria-describedby="amount" placeholder="Enter Amount">
                    </div>
                    {{-- date --}}
                    <div class="form-group showCheckBoxError">
                        <label for="date">Expense Date</label>
                        <input type="date" class="form-control" name="expenseDate" id="expenseDateCreate"
                            aria-describedby="expenseDate">
                    </div>
                    {{-- all users checkboxes --}}
                    <div class="form-check mt-2">
                        <input class="form-check-input ckbCheckAll" type="checkbox" value="" id="ckbCheckAll">
                        <label class="form-check-label" for="flexCheckDefault">
                            Select All
                        </label>
                    </div>
                    @foreach ($groupUsers as $groupUser)
                        <div class="form-check mt-2">
                            <input class="form-check-input checkBoxCommon" type="checkbox"
                                value="{{ $groupUser->id }}" id="users" name="expenseUsers[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                {{ $groupUser->name }}
                            </label>
                        </div>
                    @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitCreate">Add Expense</button>
            </div>
            </form>
        </div>
    </div>
</div>


{{-- edit expense  modal --}}
<div class="modal fade" id="edit-expense-modal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Edit Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="#" id="edit-expense-form" method="POST">
                    @method('PUT')
                    @csrf
                    {{-- title --}}
                    <input type="hidden" name="expenseId" value="" id="expenseIdHidden">
                    <input type="hidden" name="groupId" value="{{ $group->id }}" id="groupId">
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" class="form-control" name="editTitle" id="editTitle"
                            aria-describedby="nameHelp" placeholder="Enter Expense Detail">
                    </div>
                    {{-- amount --}}
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" name="editAmount" id="editAmount"
                            aria-describedby="amount" placeholder="Enter Amount">
                    </div>
                    {{-- date --}}
                    <div class="form-group showCheckBoxError">
                        <label for="date">Expense Date</label>
                        <input type="date" class="form-control" name="editExpenseDate" id="editExpenseDate"
                            aria-describedby="editExpenseDate">
                    </div>
                    {{-- all users checkboxes --}}
                    <div class="form-check mt-2">
                        <input class="form-check-input ckbCheckAll" type="checkbox" value="" id="ckbCheckAll">
                        <label class="form-check-label" for="flexCheckDefault">
                            Select All
                        </label>
                    </div>
                    @foreach ($groupUsers as $groupUser)
                        <div class="form-check mt-2">
                            <input class="form-check-input checkBoxCommon" type="checkbox"
                                value="{{ $groupUser->id }}" id="users" name="editExpenseUsers[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                {{ $groupUser->name }}
                            </label>
                        </div>
                    @endforeach
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="editSubmit">Update Expense</button>
            </div>
            </form>
        </div>
    </div>
</div>

<!--delete expense  Modal -->
{{-- <div class="modal fade" id="delete-expense-modal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Confirmation</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <h4 class="">
                    Are you sure you wanna delete this expense?
                </h4>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="submitDelete"
                    style="min-width: 100px">Delete</button>
            </div>
        </div>
    </div>
</div> --}}





{{-- pagewise script --}}
@section('page-script')
    {{-- date range picker cdn --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>

    <script>
        $(function() {

            // // custom range picker script
            $('input[name="daterange"]').daterangepicker({
                autoUpdateInput: false,
                locale: {
                    cancelLabel: 'Clear'
                }
            });
            $('input[name="daterange"]').on('apply.daterangepicker', function(ev, picker) {
                $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
                    'MM/DD/YYYY'));
            });
            $('input[name="daterange"]').on('cancel.daterangepicker', function(ev, picker) {
                $(this).val('');
            });

            // featch all expenses with ajax
            // alert('loaded');
            var groupId = $('#groupId').val();
            // console.log(groupId);
            var url = "{{ route('expense.index') }}";

            // ajax to append all expenses on page load
            var firstCallData = {
                "groupId": groupId
            }
            makeAjaxCall(firstCallData);

            function makeAjaxCall(dataTosend) {
                $.ajax({
                    url: url,
                    data: dataTosend,
                    success: function(data) {
                        // console.log('Submission was successful.');
                        // console.log('Data', data);
                        // console.log(data.expenses, 'expenses');
                        $('tbody').empty();
                        data.expenses.forEach(expense => {
                            // console.log('expense', expense);
                            $('#expense-table').append(
                                `<tr class="data_${expense.id}" >
                                <td>${expense.title}</td>
                                <td>${expense.members}</td>
                                <td>${expense.amount}</td>
                                <td>${expense.expense_date}</td>
                                <td id="actions">
                                    {{-- edit expense --}}
                                    <a class="mr-2 btn text-primary editExpenseButton"
                                        title="Edit Expense" data-expenseId="${expense.id}">
                                        <i class="fa fa-edit"></i>
                                    </a>
                                 <input type="hidden" value='${expense.id}' name='expenseId' id="${expense.id}">
                                    {{-- delete group --}}
                                    <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                                        data-expenseId="${expense.id}">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </td>
                            </tr> `
                            );
                        });
                        // append expense id to the delete modal
                        // date('d-m-Y', strtotime(expense.expense_date));
                    },
                    error: function(data) {
                        console.log('An error occurred.');
                        console.log(data);
                    },
                });

            }

            // select all check box and deselect all
            $(".ckbCheckAll").click(function() {
                $(".checkBoxCommon").prop('checked', $(this).prop('checked'));
            });

            //create expense form jquery validation + append
            var validator = $("#createExpenseForm").validate({
                rules: {
                    title: {
                        required: true,
                        // lettersonly: true
                    },
                    amount: {
                        required: true,
                        number: true
                    },
                    expenseDate: "required",
                    'expenseUsers[]': {
                        required: true,
                    },
                },
                messages: {
                    title: "Title field is required.",
                    amount: {
                        required: "Amount field is required.",
                        number: "Only numbers are allowed.",
                    },
                    expenseDate: "Expense Date is required.",
                    'expenseUsers[]': {
                        required: "Select atleast one member to continue.",
                    },
                },
                errorPlacement: function(error, element) {
                    // console.log(element.attr('name'));
                    if (element.attr("type") == "checkbox") {
                        error.insertAfter($(element).parents().siblings('.showCheckBoxError'));
                    } else {
                        // something else if it's not a checkbox
                        error.insertAfter($(element));
                    }
                },

                // create expense on submit event
                //it handles the form on submit
                submitHandler: function(form) {

                    var createExpenseForm = $('#createExpenseForm');
                    event.preventDefault();
                    $('#submitCreate').prop('disabled', true);
                    // alert('submitted');
                    // console.log(createExpenseForm.serialize())
                    // create expense ajax
                    $.ajax({
                        type: "POST",
                        url: "{{ route('expense.store') }}",
                        data: createExpenseForm.serialize(),
                        success: function(data) {
                            console.log('craete expense form data sent');
                            // console.log('create form date:', data);

                            //append the data
                            $('#expense-table').append(
                                `<tr class="data_${data.newExpense.id}">
                                    <td>${data.newExpense.title}</td>
                                    <td>${data.newExpense.members}</td>
                                    <td>${data.newExpense.amount}</td>
                                    <td>${data.newExpense.expense_date}</td>
                                    <td id="actions">
                                        {{-- edit expense --}}
                                        <a class="mr-2 btn text-primary editExpenseButton"
                                            title="Edit Expense" data-expenseId="${data.newExpense.id}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <input type="hidden" name="groupId" value="${data.newExpense.id}" id="groupId">
                                        {{-- delete group --}}
                                        <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                                            data-expenseId="${data.newExpense.id}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                              </tr> `
                            );

                            $('#create-expense-modal').modal('toggle');
                            $('#titleCreate').val('');
                            $('#amountCreate').val('');
                            $('#expenseDateCreate').val('');
                            $('.checkBoxCommon').prop('checked', false); // Unchecks it
                            $('#ckbCheckAll').prop('checked', false);
                            $('#submitCreate').prop('disabled', false);
                            toastr.success('A New Expense Has Created');
                        },
                        error: function(data) {
                            console.log('craete expense form data sent');
                            console.log(data);
                            $('#submitCreate').prop('disabled', false);
                            toastr.error('Expense was not Created please try again');
                        },
                    });
                }
            });

            //create expense modal closing
            $(document).on('hidden.bs.modal', '#create-expense-modal', function() {
                // alert('closing create modal');
                $('#titleCreate').val('');
                $('#amountCreate').val('');
                $('#expenseDateCreate').val('');
                $('.checkBoxCommon').prop('checked', false); // Unchecks it
                $('#ckbCheckAll').prop('checked', false);
                $('#submitCreate').prop('disabled', false);
                validator.resetForm();
                $('#expenseDateCreate').removeClass("error");
            });

            //  delete button on click operation for logo
            $(document).on('click', '.deleteExpenseButton', function() {
                // alert('delete logo');
                var expenseId = $(this).attr("data-expenseId");
                // console.log(expenseId);

                event.preventDefault();
                // sweet alert
                swal({
                    title: `Are you sure you want to delete this record?`,
                    text: "If you delete this, it will be gone forever.",
                    icon: "warning",
                    buttons: true,
                    dangerMode: true,
                }).then((willDelete) => {
                    if (willDelete) {
                        // form.submit();
                        $.ajax({
                            type: "delete",
                            url: "/expense/" + expenseId,
                            data: {
                                "_token": "{{ csrf_token() }}"
                            },
                            success: function(data) {
                                console.log('expense deleted');
                                console.log('delete expense', data);
                                let expenseId = data.deleteExpense.id;
                                console.log(`.data${expenseId}`);
                                $(`.data_${expenseId}`).remove();
                                // $('#delete-expense-modal').modal('toggle');
                                toastr.warning('Expense has been deleted');
                            },
                            error: function(data) {
                                console.log('craete expense form data sent');
                                console.log(data);
                                // $('#submitDelete').prop('disable', false);
                            },
                        });


                    }
                });


            });

            // delete  closing method
            // $(document).on('hidden.bs.modal', '#delete-expense-modal', function() {
            //     $('#submitDelete').attr("data-expenseId", '');
            // });

            // delete  confirm modal opreration for expense
            // $(document).on('click', '#submitDelete', function() {
            //     $('#submitDelete').prop('disable', true);
            //     // console.log($(this));
            //     var expenseId = $(this).attr("data-expenseId");

            //     console.log(expenseId, 'modal');

            //     // $.ajax({
            //     //     type: "delete",
            //     //     url: "/expense/" + expenseId,
            //     //     data: {
            //     //         "_token": "{{ csrf_token() }}"
            //     //     },
            //     //     success: function(data) {
            //     //         console.log('expense deleted');
            //     //         console.log('delete expense', data);
            //     //         let expenseId = data.deleteExpense.id;
            //     //         console.log(`.data${expenseId}`);
            //     //         $(`.data_${expenseId}`).remove();
            //     //         // $('#delete-expense-modal').modal('toggle');
            //     //         toastr.warning('Expense has been deleted');
            //     //     },
            //     //     error: function(data) {
            //     //         console.log('craete expense form data sent');
            //     //         console.log(data);
            //     //         // $('#submitDelete').prop('disable', false);
            //     //     },
            //     // });

            // });

            // edit expense attach info of editable expense
            $(document).on('click', '.editExpenseButton', handleEditOnClick);

            function handleEditOnClick(event) {
                // alert('edit clicked');
                $('#edit-expense-modal').modal('toggle');

                event.preventDefault();
                // expenseId
                let expenseId = $(this).attr("data-expenseId");
                // console.log(expenseId);

                //assining it to the value of input field in edit modal in order to featch it directly from request
                $('#expenseIdHidden').val(expenseId);

                var url = "{{ route('expense.edit', ':temp') }}";
                url = url.replace(':temp', expenseId);

                $.ajax({
                    url: url,
                    data: {
                        'expenseId': expenseId
                    },
                    success: function(data) {
                        // console.log('Submission was successful.');
                        // console.log('Data', data);
                        // console.log(data.expense, 'expense');
                        //name
                        $('#editTitle').val(data.expense.title);
                        //amount
                        $('#editAmount').val(data.expense.amount);
                        //date
                        $('#editExpenseDate').val(data.expense.expense_date);
                        $('#expenseIdHidden').val(data.expense.id)

                        let availableExpenseUsers = data.expenseUsers;
                        // console.log('expenseUsers => ', availableExpenseUsers);

                        var currentExpenseUsers = $('[name="editExpenseUsers[]"]');
                        // console.log(expenseUsers);

                        // for loop to acces the values of check boxes
                        $.each(currentExpenseUsers, function() {
                            // check if the checkbox is checked
                            if (!($(this).is(':checked'))) {
                                let currentId = $(this).val();
                                // console.log('currentId =>', currentId);

                                // for loop to compare the previously selected once to current
                                availableExpenseUsers.forEach(availableUser => {
                                    // console.log(availableUser.id);
                                    let availableId = availableUser.id;
                                    // if they match then make it checked
                                    if (availableId == currentId) {
                                        $(this).prop('checked', true);
                                    }
                                });

                            }
                        });
                    },
                    error: function(data) {
                        console.log('An error occurred.');
                        console.log(data);
                    },
                });
            }

            // validate  edit form and send it to controller in submit handler
            var editValidator = $("#edit-expense-form").validate({
                rules: {
                    editTitle: {
                        required: true,
                        // lettersonly: true
                    },
                    editAmount: {
                        required: true,
                        number: true
                    },
                    editExpenseDate: "required",
                    'editExpenseUsers[]': {
                        required: true,
                    },
                },
                messages: {
                    editTitle: "Title field is required.",
                    editAmount: {
                        required: "Amount field is required.",
                        number: "Only numbers are allowed.",
                    },
                    editExpenseDate: "Expense Date is required.",
                    'editExpenseUsers[]': {
                        required: "Select atleast one member to continue.",
                    },
                },
                errorPlacement: function(error, element) {
                    // console.log(element.attr('name'));
                    if (element.attr("type") == "checkbox") {
                        error.insertAfter($(element).parents().siblings('.showCheckBoxError'));
                    } else {
                        // something else if it's not a checkbox
                        error.insertAfter($(element));
                    }
                },

                // edit expense on submit event
                //it handles the form on submit
                submitHandler: function(form) {

                    var editExpenseForm = $('#edit-expense-form');
                    event.preventDefault();
                    // $('#editSubmit').prop('disabled', true);
                    // alert('submitted');
                    // console.log(editExpenseForm.serialize())
                    // edit expense ajax
                    $.ajax({
                        type: "POST",
                        url: "{{ route('expense.update', 1) }}",
                        data: editExpenseForm.serialize(),
                        success: function(data) {
                            // console.log('edit expense form data sent');
                            // console.log('edit form data:', data);

                            // append the data
                            let members = data.updateExpense.editExpenseUsers.length + 1;
                            $(`.data_${data.updateExpense.expenseId}`).replaceWith(
                                `<tr class="data_${data.updateExpense.expenseId}">
                                    <td>${data.updateExpense.editTitle}</td>
                                    <td>${members}</td>
                                    <td>${data.updateExpense.editAmount}</td>
                                    <td>${data.updateExpense.editExpenseDate}</td>
                                    <td id="actions">
                                        {{-- edit expense --}}
                                        <a class="mr-2 btn text-primary editExpenseButton"
                                            title="Edit Expense" data-expenseId="${data.updateExpense.expenseId}">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <input type="hidden" name="groupId" value="${data.updateExpense.expenseId}" id="groupId">
                                        {{-- delete group --}}
                                        <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                                            data-expenseId="${data.updateExpense.expenseId}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr> `
                            );

                            // rebinding the event after succes
                            // $(document).off('click', '.editExpenseButton',
                            //     handleEditOnClick).on('click', '.editExpenseButton',
                            //     handleEditOnClick);



                            // didn't work

                            // $(`.data_${data.updateExpense.expenseId}`).html(
                            //     `<td>${data.updateExpense.editTitle}</td>
                        //     <td>${members}</td>
                        //     <td>${data.updateExpense.editAmount}</td>
                        //     <td>${data.updateExpense.editExpenseDate}</td>
                        //     <td id="actions">
                        //         {{-- edit expense --}}
                        //         <a class="mr-2 btn text-primary editExpenseForm"
                        //             title="Edit Expense" data-expenseId="${data.updateExpense.expenseId}">
                        //             <i class="fa fa-edit"></i>
                        //         </a>
                        //         <input type="hidden" name="groupId" value="${data.updateExpense.expenseId}" id="groupId">
                        //         {{-- delete group --}}
                        //         <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                        //             data-expenseId="${data.updateExpense.expenseId}">
                        //             <i class="fa fa-trash"></i>
                        //         </button>
                        //     </td> `
                            // );


                            // didn't work


                            // var replacement = `<tr class="data_${data.updateExpense.expenseId}">
                        //     <td>${data.updateExpense.editTitle}</td>
                        //     <td>${members}</td>
                        //     <td>${data.updateExpense.editAmount}</td>
                        //     <td>${data.updateExpense.editExpenseDate}</td>
                        //     <td id="actions">
                        //         {{-- edit expense --}}
                        //         <a class="mr-2 btn text-primary editExpenseForm"
                        //             title="Edit Expense" data-expenseId="${data.updateExpense.expenseId}">
                        //             <i class="fa fa-edit"></i>
                        //         </a>
                        //         <input type="hidden" name="groupId" value="${data.updateExpense.expenseId}" id="groupId">
                        //         {{-- delete group --}}
                        //         <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                        //             data-expenseId="${data.updateExpense.expenseId}">
                        //             <i class="fa fa-trash"></i>
                        //         </button>
                        //     </td>
                        // </tr> `;

                            // var original = $(`.data_${data.updateExpense.expenseId}`)
                            //     .replaceWith(replacement);

                            // replacement
                            //     .click(function() {
                            //         replacement.replaceWith(original);
                            //         original.click(replace);
                            //     });





                            $('#edit-expense-modal').modal('toggle');
                            $('#editTitle').val('');
                            $('#editAmount').val('');
                            $('#editExpenseDate').val('');
                            $('.checkBoxCommon').prop('checked', false); // Unchecks it
                            $('#ckbCheckAll').prop('checked',
                                false); //uncheck select all check box
                            $('#EditSubmit').prop('disabled', false);
                            toastr.info('Expense Edited SuccesFully');
                        },
                        error: function(data) {
                            console.log('edit expense form data not sent');
                            console.log(data);
                            $('#submitCreate').prop('disabled', false);
                        },
                    });
                }
            });

            // edit expense closing
            $(document).on('hidden.bs.modal', '#edit-expense-modal', function() {
                // alert('closing edit modal');
                $('#editTitle').val('');
                $('#editAmount').val('');
                $('#editExpenseDate').val('');
                $('.checkBoxCommon').prop('checked', false); // Unchecks it
                $('#ckbCheckAll').prop('checked', false);
                $('#EditSubmit').prop('disabled', false);
                editValidator.resetForm();
                $('#editExpenseDate').removeClass("error");
            });

            // search expenses
            // $('#search').on('blur', function() {
            $('#search').blur(function() {
                // alert('blur triggered');
                // let value = $(this).val();
                // //ajax to search
                // searchData = {
                //     "groupId": groupId,
                //     'search': value,
                // }
                // makeAjaxCall(searchData);
                applyExpenseFilter();
            });

            //  user wise filter and datewise filter
            $(document).on('click', '#filterButton', function() {

                // let filterUser = $('#filterUser').val();
                // let dateRange = $('#dateRange').val();
                // let filterData = {
                //     'filterUser': filterUser,
                //     'dateRange': dateRange,
                //     'groupId': groupId,
                // };
                // makeAjaxCall(filterData);
                applyExpenseFilter();

            });
            // for both event i want same thing
            function applyExpenseFilter() {
                let filterUser = $('#filterUser').val();
                let dateRange = $('#dateRange').val();
                let searchValue = $('#search').val();
                let filterData = {
                    'filterUser': filterUser,
                    'dateRange': dateRange,
                    'groupId': groupId,
                    'search': searchValue,
                };
                makeAjaxCall(filterData);
            }
        })
    </script>
@endsection
