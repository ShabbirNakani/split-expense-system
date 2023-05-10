@extends('layouts.master')
@section('title', 'My Friends')
@section('pagewise-links')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

@endsection

@section('page-content-title', 'My Friends')

@section('page-path', 'My Friends')

{{--  main content --}}
@section('main-content')
    <div class="col-12">
        {{-- <div class="row">
            <div class="col-12 mb-3">
                <button type="button" class="btn btn-primary float-right" id="addFriendButton">Add Friend</button>
            </div>
        </div> --}}
        <div class="row">
            <div class="col-4">
                <input class="form-control" type="text" id="search" name="search" placeholder="Search...">
            </div>
            <div class="col-3">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <label class="input-group-text" for="inputGroupSelect01">Groups</label>
                    </div>
                    <select class="custom-select" id="filterUser">
                        <option selected value="null">Choose...</option>
                        {{-- @foreach ($friendsGroupsWithUsers as $groups) --}}
                        {{-- @foreach ($groups['users'] as $users) --}}
                        {{-- <option value="{{ $groups['id'] }}">{{ $groups['title'] }}</option> --}}
                        {{-- @endforeach --}}
                        {{-- @endforeach --}}
                    </select>
                </div>
            </div>
        </div>
        <div class="card mt-3">
            <div class="card-body">
                <div class="row tableHolder mt-2">
                    <div class="col-12">
                        <table id="expense-table" class="table table-bordered  table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Groups</th>
                                    <th>Total Amount</th>
                                    <th>Settele</th>
                                </tr>
                            </thead>
                            <tbody class="tbody">
                                @foreach ($friends as $friend)
                                    <tr>
                                        <td>{{ $friend['name'] }}</td>
                                        <td class="groupIdContainer">
                                            <div class="row">

                                                @foreach ($friendsGroupsWithUsers as $groups)
                                                    {{-- {{ dd($groups) }} --}}
                                                    @foreach ($groups['users'] as $users)
                                                        {{-- {{$users['id']}} --}}
                                                        {{-- if group users id matches the friend id then show that groups name --}}
                                                        @if ($users['id'] == $friend['id'])
                                                            <div class="col mb-1" data-group-id="{{ $groups['id'] }}">
                                                                <h4>
                                                                    <span
                                                                        class="badge badge-light">{{ $groups['title'] }}</span>
                                                                </h4>
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endforeach
                                            </div>
                                        </td>
                                        <td
                                            @isset($friend['status'])
                                            @if ($friend['status'] == 'owe') class="text-success"
                                            @elseif($friend['status'] == 'pay') class="text-danger"
                                            @endif @endisset>
                                            @php
                                                $friend['remainigAmount'] = isset($friend['remainigAmount']) ? $friend['remainigAmount'] : 0;
                                            @endphp
                                            {{ $friend['remainigAmount'] }}
                                        </td>
                                        <td id="actions">
                                            {{-- settelment modal button --}}
                                            <button name="settle" value="Settle"
                                                class="btn  mr-2  text-primary open-settel-modal"
                                                data-friend-id="{{ $friend['id'] }}">
                                                <i class="fa fa-money" style="font-size:24px"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

{{-- settel expense modal --}}
<div class="modal fade" id="settel-expense-modal" data-backdrop="static" tabindex="-1" role="dialog"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Settel Expense</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="setteleExpenseForm" method="POST">
                    @csrf
                    <div class="form-check mb-3">
                        <div class="row">
                            <div class="col-12">
                                <input class="form-check-input ckbCheckAll" type="checkbox" value=""
                                    id="ckbCheckAll">
                                <label class="form-check-label" for="flexCheckDefault">
                                    Select All
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="append-checkBox">
                        @foreach ($groupsWithExpenses as $groups)
                            <div class="form-check">
                                <div class="row">
                                    <div class="col-8">
                                        <input class="form-check-input checkBoxCommon" type="checkbox"
                                            value="{{ $groups['id'] }}" id="users" name="expenseUsers[]">
                                        <label class="form-check-label" for="flexCheckDefault">
                                            {{ $groups['title'] }}
                                        </label>
                                    </div>
                                    <div class="col-4">
                                        @php
                                            $totalExpense = 0;
                                            foreach ($groups['expenses'] as $expense) {
                                                $totalExpense += $expense['amount'];
                                            }
                                        @endphp
                                        <h4><span class="badge badge-success"> {{ $totalExpense }}</span></h4>
                                    </div>
                                    {{-- <div class="col-4"> --}}

                                    {{-- </div> --}}
                                </div>
                            </div>
                        @endforeach
                    </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="submitSettel">Settel</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- pagewise sctipt --}}
@section('page-script')
    <script>
        $(function() {
            // settelment modal open
            $(document).on('click', '.open-settel-modal', handleSettelOnClick);

            function handleSettelOnClick(event) {
                // alert('settel modal clicked');
                $('#settel-expense-modal').modal('toggle');
                // friend id
                let friendId = $(this).attr('data-friend-id');
                //  all the divs that contains th data-group-id attribute for each row
                let groupids = [];
                $(this).parent().siblings(".groupIdContainer").children('.row').children('div').each(
                    function(index, value) {
                        // console.log($(this).attr('data-group-id'));
                        groupids.push($(this).attr('data-group-id'));
                    });

                console.log('friend id : ', friendId);
                console.log('group id : ', groupids);
                // featch settele modal detail
                $.ajax({
                    type: "get",
                    url: "{{ route('settel-modal-data') }}",
                    data: {
                        'friendId': friendId,
                        'groupids': groupids,
                    },
                    success: function(data) {
                        // alert(result.d);
                        console.log(data);

                        // attach the details to the settel modal

                    },
                    error: function(data) {
                        console.log(data);
                    }
                });
            }
            // select all
            $(".ckbCheckAll").click(function() {
                $(".checkBoxCommon").prop('checked', $(this).prop('checked'));
            });
        })
    </script>
@endsection
