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
                <form id="createExpenseForm" method="POST">
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
                    <div class="form-group">
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
                    <div class="showCheckBoxError mt-2"></div>
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
                        <input type="text" class="form-control" name="title" id="editTitle"
                            aria-describedby="nameHelp" placeholder="Enter Expense Detail">
                    </div>
                    {{-- amount --}}
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <input type="number" class="form-control" name="amount" id="editAmount"
                            aria-describedby="amount" placeholder="Enter Amount">
                    </div>
                    {{-- date --}}
                    <div class="form-group ">
                        <label for="date">Expense Date</label>
                        <input type="date" class="form-control" name="expenseDate" id="editExpenseDate"
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
                                value="{{ $groupUser->id }}" id="users" name="expenseUsers[]">
                            <label class="form-check-label" for="flexCheckDefault">
                                {{ $groupUser->name }}
                            </label>
                        </div>
                    @endforeach
                    <div class="showCheckBoxError"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary" id="editSubmit">Update Expense</button>
            </div>
            </form>
        </div>
    </div>
</div>

{{-- pagewise script --}}
@section('page-script')
    {{-- date range picker cdn --}}
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    {{-- <!-- Laravel Javascript Validation --> --}}
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js') }}"></script>

    <!-- Scripts -->
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script> --}}
    {{-- <script src="//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.1/js/bootstrap.min.js"></script> --}}

    {!! JsValidator::formRequest('App\Http\Requests\ExpenseRequest', '#createExpenseForm') !!}
    {!! JsValidator::formRequest('App\Http\Requests\ExpenseRequest', '#edit-expense-form') !!}
    <script>
        var authUserId = {{ auth()->user()->id }}
    </script>
    <script src="{{ asset('js/myExpenses.js') }}"></script>
@endsection
