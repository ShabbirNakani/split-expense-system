$(function () {


    // make an ajax call to add table first time not used anymore

    // $.ajax({
    //     type: "GET",
    //     url: "/friends",
    //     // data: {
    //     //     'data': varName,
    //     // },
    //     dataType: "json",
    //     success: function (data) {
    //         // alert('ajax send')
    //         console.log(data);
    //         // add the html response to the view
    //         $('#expense_container').html(data.html);
    //     },
    //     error: function (data) {
    //         console.log(data);
    //     },
    // });


    // data table
    var expense_table = $('#expense-table').dataTable({
        "aoColumnDefs": [
            { "bSortable": false, "aTargets": [2, 3] },
            { "bSearchable": false, "aTargets": [2, 3] }
        ],
        processing: true,
        serverSide: true,
        ajax: "/friends",
        columns: [
            { data: 'name', name: 'name' },
            { data: 'groupNames', name: 'groups', 'className': "groupIdContainer" },
            { data: 'remainigAmount', name: 'remainigAmount', 'className': `data_amount` },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        createdRow: function (row, data, index) {
            //
            // if the second column cell is blank apply special formatting
            //
            // console.log(data.id);
            $(row).addClass(`data_${data.id}`);
        }
    });


    // settelment modal open

    $(document).on('click', '.open-settel-modal', handleSettelOnClick);


    function handleSettelOnClick(event) {
        // alert('settel modal clicked');
        $('#settel-expense-modal').modal('toggle');
        // friend id
        var friendId = $(this).attr('data-friend-id');
        //  all the divs that contains th data-group-id attribute for each row
        var groupids = [];
        $(this).parent().siblings(".groupIdContainer").children('.row').children('div').each(
            function (index, value) {
                // console.log($(this).attr('data-group-id'));
                groupids.push($(this).attr('data-group-id'));
            });
        // featch settele modal detail
        $.ajax({
            type: "get",
            // url: "{{ route('settel.modal.data') }}",
            url: '/settel-modal-data',
            data: {
                'friendId': friendId,
                'groupids': groupids,
            },
            success: function (data) {
                console.log(data);
                // apend select all button
                $('.append-checkBox').append(`
                    <div class="form-check mb-3">
                        <div class="row" id='selectAllGroupsCheckBox'>
                            <div class="col-12">
                                <input class="form-check-input ckbCheckAll" type="checkbox" value=""
                                    id="ckbCheckAll">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>
                `);
                // console.log(data.groupsWithExpenses);
                // if (data.groupsWithExpenses.length >= 1) {
                if (Object.keys(data.groupsWithExpenses).length >= 1) {
                    $.each(data.groupsWithExpenses, function (key, group) {
                        // console.log(group);
                        if (group.status != 'default') {
                            $('.append-checkBox').append(`
                            <div class="form-check">
                                <div class="row">
                                    <div class="col-8">
                                        <input class="form-check-input checkBoxCommon" type="checkbox" value="${group.id}" id="groups" name="groups[]">
                                        <label class="form-check-label" for="flexCheckDefault">${group.title}</label>
                                    </div>
                                    <div class="col-4">
                                        <h4><span class="badge badge-success">${group.remainingAmount}</span></h4>
                                    </div>
                                </div>
                            </div>
                      `);
                        }

                    });
                } else {
                    // pass a msg that every expenses have been settled
                    $('.append-checkBox').html(`<h3 class="text-success"> All Expenses Are Setteled </h3>`)
                }
                // console.log(data.friendId);
                // settel button has friendid
                $('#submitSettel').attr('data-friend-id', data.friendId);
            },
            error: function (data) {
                console.log(data);
            }
        });
    }

    // settel modal closing
    $("#settel-expense-modal").on("hidden.bs.modal", function () {
        $('.checkBoxCommon').prop('checked', false);
        $(".ckbCheckAll").prop('checked', false);
        $('.append-checkBox').empty();
        $('#submitSettel').data('friend-id', '');
    });

    // select all
    $(".ckbCheckAll").click(function () {
        $(".checkBoxCommon").prop('checked', $(this).prop('checked'));
    });

    // settele expense
    $(document).on('click', '#submitSettel', function (event) {

        event.preventDefault();
        let friendId = $(this).attr('data-friend-id');
        var form = $('#setteleExpenseForm').serializeArray();
        form.push({
            'name': 'friendId',
            'value': friendId
        });
        console.log(form);
        // return false
        console.log(friendId);
        $.ajax({
            type: "get",
            // url: "{{ route('settel.expense') }}",
            url: '/settel-expense',
            data: form,
            success: function (data) {
                console.log(data);
                // update the expense to zero for setteled groups
                // $(`data_${data.friendId}`).closest('.data_amount').html(data.remainingAmount);
                // console.log($(`data_${data.friendId}`).closest('.data_amount').html(data.remainingAmount));
                // $(`.data_amount_${data.friendId}`).html(`${data.remainingAmount}`);
                $('#expense-table').DataTable().ajax.reload(null, false);
                // class
                if (data.remainingAmount > 0) {
                    // remove previous class
                    // $(`.data_amount_${data.friendId}`).removeClass();
                    // add class succes
                    // $(`.data_amount_${data.friendId}`).addClass();
                } else if (data.remainingAmount < 0) {
                    // remove previous class
                    // add class danged
                } else {
                    // remove previous class
                }
                $('#settel-expense-modal').modal('hide');
                toastr.success(`Amount Settled With ${titleCase(data.friendName)}`);
            },
            error: function (data) {
                console.log(data);
                $('#settel-expense-modal').modal('hide');
            }
        });
    });

    // disable settel if no group is selected
    $(document).on('change', '.checkBoxCommon', function () {
        var checkedBox = $('input[name="groups[]"]:checked').length;
        // console.log(checkedBox);
        if (checkedBox >= 1) {
            $('#submitSettel').prop('disabled', false);
        } else {
            $('#submitSettel').prop('disabled', true);
        }
    });

    // groupWise Friends Filter
    $(document).on('change', '#groupwiseFilter', function (event) {
        event.preventDefault();
        // alert('filter works');

        let groupFilter = $('#groupwiseFilter').val();
        console.log(groupFilter);
        if (groupFilter != 'null') {
            // $.ajax({
            //     type: "get",
            //     url: "/friends",
            //     data: {
            //         'groupFilter': groupFilter,
            //     },
            //     dataType: "json",
            //     success: function (result) {
            //     }
            // });
        } else {
            toastr.info('No Filter is Selected');
        }
    });

    // function to upper case first letter of each words in a string
    function titleCase(str) {
        var splitStr = str.toLowerCase().split(' ');
        for (var i = 0; i < splitStr.length; i++) {
            // You do not need to check if i is larger than splitStr length, as your for does that for you
            // Assign it back to the array
            splitStr[i] = splitStr[i].charAt(0).toUpperCase() + splitStr[i].substring(1);
        }
        // Directly return the joined string
        return splitStr.join(' ');
    }
})
