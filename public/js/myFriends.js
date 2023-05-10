$(function () {

    let table = new DataTable('#expense-table');

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
        // console.log('friend id : ', friendId);
        // console.log('group id : ', groupids);
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
                // console.log(data.groupsWithExpenses);

                $.each(data.groupsWithExpenses, function (key, group) {
                    // console.log(group);

                    $('.append-checkBox').append(`
                  <div class="form-check">
                       <div class="row">
                            <div class="col-8">
                                    <input class="form-check-input checkBoxCommon" type="checkbox" value="${group.id}" id="groups" name="groups[]">
                                    <label class="form-check-label" for="flexCheckDefault">
                                        ${group.title}
                                    </label>
                            </div>
                                <div class="col-4">
                                    <h4><span class="badge badge-success">${group.remainingAmount}</span></h4>
                                </div>
                            </div>
                        </div>
                  `);

                });
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
                // $(`.data_amount_${data.friendId}`).html('0');
                // remove or add class
            },
            error: function (data) {
                console.log(data);
            }
        });
    });
    $(document).on('change', '.checkBoxCommon', function () {
        var checkedBox = $('input[name="groups[]"]:checked').length;
        // console.log(checkedBox);
        if (checkedBox >= 1) {
            $('#submitSettel').prop('disabled', false);
        } else {
            $('#submitSettel').prop('disabled', true);
        }
    });


})
