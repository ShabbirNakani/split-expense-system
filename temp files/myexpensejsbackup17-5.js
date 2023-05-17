$(function () {
    // var $j = jQuery.noConflict();
    // // custom range picker script
    $('input[name="daterange"]').daterangepicker({
        autoUpdateInput: false,
        locale: {
            cancelLabel: 'Clear'
        }
    });
    $('input[name="daterange"]').on('apply.daterangepicker', function (ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format(
            'MM/DD/YYYY'));
    });
    $('input[name="daterange"]').on('cancel.daterangepicker', function (ev, picker) {
        $(this).val('');
    });

    // featch all expenses with ajax
    // alert('loaded');
    var groupId = $('#groupId').val();
    // console.log(groupId);
    // var url = "{{ route('expense.index') }}";
    var url = "/expense";

    // ajax to append all expenses on page load
    var firstCallData = {
        "groupId": groupId
    }
    makeAjaxCall(firstCallData);

    function makeAjaxCall(dataTosend) {
        // console.log(url);
        $.ajax({
            url: url,
            data: dataTosend,
            success: function (data) {
                // console.log('Submission was successful.');
                // console.log('Data', data);
                // console.log(data.expenses, 'expenses');
                $('tbody').empty();
                data.expenses.forEach(expense => {
                    // console.log('expense', expense);

                    var appendRow = `<tr class="data_${expense.id}" >
                        <td>${expense.title}</td>
                        <td>${expense.members}</td>
                        <td>${expense.amount}</td>
                        <td>${expense.expense_date}</td>
                        <td id="actions">`;

                    if (authUserId == `${expense.user_id}`) {
                        appendRow += `
                            <a class="mr-2 btn text-primary editExpenseButton"
                                title="Edit Expense" data-expenseId="${expense.id}">
                                <i class="fa fa-edit"></i>
                            </a>
                         <input type="hidden" value='${expense.id}' name='expenseId' id="${expense.id}">
                            <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                                data-expenseId="${expense.id}">
                                <i class="fa fa-trash"></i>
                            </button>
                        `;

                    }
                    appendRow += ` </td></tr>`;

                    $('#expense-table').append(appendRow);
                });
                // append expense id to the delete modal
                // date('d-m-Y', strtotime(expense.expense_date));
            },
            error: function (data) {
                console.log('An error occurred.');
                console.log(data);
            },
        });

    }

    // select all check box and deselect all
    $(".ckbCheckAll").click(function () {
        $(".checkBoxCommon").prop('checked', $(this).prop('checked'));
    });

    create expense form jquery validation + append
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
        errorPlacement: function (error, element) {
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
        submitHandler: function (form) {

            var createExpenseForm = $('#createExpenseForm');
            // event.preventDefault();
            $('#submitCreate').prop('disabled', true);
            // alert('submitted');
            // console.log(createExpenseForm.serialize())
            // create expense ajax
            $.ajax({
                type: "POST",
                // url: "{{ route('expense.store') }}",
                url: "/expense",
                data: createExpenseForm.serialize(),
                success: function (data) {
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
                                <a class="mr-2 btn text-primary editExpenseButton"
                                    title="Edit Expense" data-expenseId="${data.newExpense.id}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <input type="hidden" name="groupId" value="${data.newExpense.id}" id="groupId">
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
                error: function (data) {
                    console.log('craete expense form data sent');
                    console.log(data);
                    $('#submitCreate').prop('disabled', false);
                    toastr.error('Expense was not Created please try again');
                },
            });
        }
    });

    //create expense modal closing
    $(document).on('hidden.bs.modal', '#create-expense-modal', function () {
        // alert('closing create modal');
        $('#titleCreate').val('');
        $('#amountCreate').val('');
        $('#expenseDateCreate').val('');
        $('.checkBoxCommon').prop('checked', false); // Unchecks it
        $('#ckbCheckAll').prop('checked', false);
        $('#submitCreate').prop('disabled', false);
        // validator.resetForm();
        $('#expenseDateCreate').removeClass("error");
    });

    //  delete button on click operation for logo
    $(document).on('click', '.deleteExpenseButton', function () {
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
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    success: function (data) {
                        console.log('expense deleted');
                        console.log('delete expense', data);
                        let expenseId = data.deleteExpense.id;
                        console.log(`.data${expenseId}`);
                        $(`.data_${expenseId}`).remove();
                        // $('#delete-expense-modal').modal('toggle');
                        toastr.warning('Expense has been deleted');
                    },
                    error: function (data) {
                        console.log('craete expense form data sent');
                        console.log(data);
                        // $('#submitDelete').prop('disable', false);
                    },
                });


            }
        });



    });

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

        // var url = "{{ route('expense.edit', ':temp') }}";
        // url = url.replace(':temp', expenseId);
        var url = `/expense/${expenseId}/edit`;

        $.ajax({
            url: url,
            data: {
                'expenseId': expenseId
            },
            success: function (data) {
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

                var currentExpenseUsers = $('[name="expenseUsers[]"]');
                // console.log(expenseUsers);

                // for loop to acces the values of check boxes
                $.each(currentExpenseUsers, function () {
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
            error: function (data) {
                console.log('An error occurred.');
                console.log(data);
            },
        });
    }

    // validate  edit form and send it to controller in submit handler
    var editValidator = $("#edit-expense-form").validate({
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
        errorPlacement: function (error, element) {
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
        submitHandler: function (form) {

            var editExpenseForm = $('#edit-expense-form');
            // event.preventDefault();
            // $('#editSubmit').prop('disabled', true);
            // alert('submitted');
            // console.log(editExpenseForm.serialize())
            // edit expense ajax
            $.ajax({
                type: "POST",
                // url: "{{ route('expense.update', 1) }}",
                url: "/expense/1  ",
                type: 'PUT',
                data: editExpenseForm.serialize(),
                success: function (data) {
                    // console.log('edit expense form data sent');
                    // console.log('edit form data:', data);

                    // append the data
                    let members = data.updateExpense.expenseUsers.length + 1;
                    $(`.data_${data.updateExpense.expenseId}`).replaceWith(
                        `<tr class="data_${data.updateExpense.expenseId}">
                            <td>${data.updateExpense.title}</td>
                            <td>${members}</td>
                            <td>${data.updateExpense.amount}</td>
                            <td>${data.updateExpense.expenseDate}</td>
                            <td id="actions">
                                <a class="mr-2 btn text-primary editExpenseButton"
                                    title="Edit Expense" data-expenseId="${data.updateExpense.expenseId}">
                                    <i class="fa fa-edit"></i>
                                </a>
                                <input type="hidden" name="groupId" value="${data.updateExpense.expenseId}" id="groupId">
                                <button name="delete" value="delete" class="btn  mr-2  text-danger deleteExpenseButton"
                                    data-expenseId="${data.updateExpense.expenseId}">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr> `
                    );

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
                error: function (data) {
                    console.log('edit expense form data not sent');
                    console.log(data);
                    $('#submitCreate').prop('disabled', false);
                },
            });
        }
    });

    // edit expense closing
    $(document).on('hidden.bs.modal', '#edit-expense-modal', function () {
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
    $('#search').blur(function () {
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
    $(document).on('click', '#filterButton', function () {

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
