$(function () {
    //featching edit group details and appending
    $('.edit-group-link').on('click', function (event) {
        event.preventDefault();
        // alert('clicked');

        //opening model with on click
        $('.edit-group-modal').modal('show')

        //featchingh groupId from Hidden field
        let groupId = $(this).siblings('input[name=groupId]').val();

        //assining it to the value of input field in edit modal in order to featch it directly from request
        $('#hiddenGroupIdEditModal').val(groupId);
        var groupIdHidden = $('#hiddenGroupIdEditModal').val()
        // console.log(groupIdHidden, 'hidden');
        // console.log(groupId);
        // var url = "{{ route('groups.edit', ':temp') }}";
        // url = url.replace(':temp', groupId);
        url= `/groups/${groupId}/edit`;
        $.ajax({
            url: url,
            data: groupId,
            success: function (data) {
                // console.log('Submission was successful.');
                console.log('Data', data);
                // console.log(data.group, 'groups');
                //name
                let groupData = data.groupWithUsers[0];
                // title
                $('#titleEdit').val(groupData.title);
                //discription
                $('#discriptionEdit').val(groupData.discription);
                let groupUsersPivot = groupData.group_users;
                //users
                // console.log(groupUsersPivot, 'pivot');
                groupUsersPivot.forEach(user => {
                    // console.log(user.group_users[0],'users');
                    let userDetails = user.group_users[0];
                    // console.log(userDetails);
                    if (userDetails.id != `{{ Auth::user()->id }}`) {

                        //delete those elemets from the list first then append as selected
                        $(`#selectEdit option[value='${userDetails.id}']`)
                            .remove();

                        // create the option and append to Select2
                        //note we can not directly select any option using azax so we need to create a new option and append it
                        var option = new Option(userDetails.name,
                            userDetails.id, true, true);
                        $('#selectEdit').append(option).trigger(
                            'change');

                        // manually trigger the `select2:select` event
                        $('#selectEdit').trigger({
                            type: 'select2:select',
                            params: {
                                data: data
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
    });

    // deselecting upper selected group users on close method of modal
    $(".edit-group-modal").on("hidden.bs.modal", function () {

        // alert('modal closed');
        $("#selectEdit").each(function () { //added a each loop here
            $(this).select2('destroy').val("").select2();
        });
        $('#titleEdit').val('');
        $('#discriptionEdit').val('');
        editValidator.resetForm();
    });


    //create form jquery validation
    careateValidator = $("#createGroupForm").validate({
        rules: {
            title: "required",
            discription: "required",
            "users[]": "required"

        },
        messages: {
            title: "Title field is required.",
            discription: "Discription field is required.",
            "users[]": "Atleast one user is required."
        },
        // errorPlacement: function(error, element) {
        //     if (element.is(":radio")) {
        //         error.appendTo(element.parents('.form-group'));
        //     } else { // This is the default behavior
        //         error.insertAfter(element);
        //     }
        // },
        submitHandler: function (form) {
            form.submit();
        }
    });

    //edit form jquery validation
    editValidator = $("#editGroupForm").validate({
        rules: {
            title: "required",
            discription: "required",
            "users[]": {
                required: true,
                checExpensesBeforeEdit: true,
            }

        },
        messages: {
            title: "Title field is required.",
            discription: "Discription field is required.",
            "users[]": {
                required: "Atleast one user is required."
            }
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    // create expense closing
    $("#createGroupModal").on("hidden.bs.modal", function () {
        $('#titleCreate').val('');
        $('#discriptionCreate').val('');

        careateValidator.resetForm();
        // $("#allGroupUsersCreate").select2("val", "");
        // $("#allGroupUsersCreate").empty().trigger('change')

    });

    // adding custome method for validation
    var response;
    $.validator.addMethod("checExpensesBeforeEdit", function (value, element) {
        // response = checkExpense();
        let groupId = $('#hiddenGroupIdEditModal').val();
        let groupUsers = $('#selectEdit').val();
        console.log(groupUsers);
        $.ajax({
            type: "get",
            // url: "{{ route('checkExpensesBeforeEdit') }}",
            url: "{{ route('checkExpensesBeforeEdit') }}",
            async: false,
            data: {
                'groupId': groupId,
                'groupUsers': groupUsers,
            },
            success: function (msg) {
                //If username exists, set response to true
                console.log(msg, 'succes');
                // console.log(msg.isvalid);
                response = (msg.isvalid == true) ? true : false;
            },
            error: function (msg) {
                console.log(msg);
            },
        });
        return response;
    },
        "Expenses have not been setteled"
    );
})
