<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\GroupList;
use App\Models\SplitExpense;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\Console\Input\Input;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        // dd($request->all());
        //finding the group ids
        $groupId = $request->groupId;

        // building a query object
        // $query = Expense::whereIn('id', $expensesIdArray)->orderBy('created_at', 'asc');

        $query = Expense::whereGroupListId($groupId)->orderBy('created_at', 'asc');

        // search expseses (live search)
        // live search logic
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->Where(function ($subquery) use ($search) {
                $subquery->Where('amount', 'LIKE', '%' . $search . "%")
                    ->orwhere('title', 'LIKE', '%' . $search . "%")
                    ->orWhere('expense_date', 'LIKE', '%' . $search . "%")
                    ->orWhere('members', 'LIKE', '%' . $search . "%");
            });
        }

        // user filter and date range filter
        if ($request->has('dateRange') && $request->dateRange) {
            $range =  explode(' - ', $request->dateRange);
            $startDate = \Carbon\Carbon::parse($range[0])->format('Y-m-d H:i:s');
            $endDate = \Carbon\Carbon::parse($range[1])->format('Y-m-d H:i:s');
            $query->whereBetween('expense_date', [$startDate, $endDate]);
        }

        if ($request->has('filterUser') && $request->filterUser !== 'null') {
            $query->whereUserId($request->filterUser);
        }
        // get the query
        $expenses = $query->get();
        return response()->json([
            'expenses' => $expenses
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //counting members => +1 for meeeee
        $members = count($request->expenseUsers) + 1;
        $creatorId = Auth::user()->id;
        // create new expens
        $newExpense = Expense::create([
            'title' => $request->title,
            'amount' => $request->amount,
            'user_id' => $creatorId,
            'group_list_id' => $request->groupId,
            'expense_date' => $request->expenseDate,
            'members' => $members,
        ]);

        //add new split expense
        $splitExpense = number_format((float)$request->amount / $members, 2, '.', '');
        // dd($splitExpense);

        //creating splitexpense users
        foreach ($request->expenseUsers as $user_id) {
            SplitExpense::create([
                'user_id' => $user_id,
                'receiver_id' => $creatorId,
                'expense_id' => $newExpense->id,
                'group_list_id' => $newExpense->group_list_id,
                'amount' => $splitExpense,
                'status' => 'owe',
                'is_Settled' => 'notSettled',
            ]);
        }

        // entry or meee
        SplitExpense::create([
            'user_id' => $creatorId,
            'receiver_id' => $creatorId,
            'expense_id' => $newExpense->id,
            'group_list_id' => $newExpense->group_list_id,
            'amount' => $splitExpense,
            'status' => 'owe',
            'is_Settled' => 'Settled',
        ]);

        // dd($newExpense->id);
        return response()->json(['newExpense' => $newExpense]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // dd($id);
        $creatorId = Auth::user()->id;
        $expense = Expense::find($id)->toArray();

        $expenseUsers = User::select('users.*')
            ->join('split_expenses', 'split_expenses.user_id', '=', 'users.id')
            ->where('split_expenses.expense_id', '=', "$id")
            ->where('users.id', '!=', $creatorId)
            ->get();

        return response()->json(['expense' => $expense, 'expenseUsers' => $expenseUsers]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $data = $request->input();
        $userIds = $data['editExpenseUsers'];
        $members = count($data['editExpenseUsers']) + 1;
        $splitExpense = (float) number_format(($data['editAmount'] / $members), 2, '.', '');
        $currentUsersIds = SplitExpense::where('group_list_id', $data['groupId'])
            ->where('user_id', '!=', Auth::user()->id)
            ->where('expense_id', $data['expenseId'])
            ->pluck('user_id')
            ->toArray();
        // finding the diffrence
        $removedUsers = array_diff($currentUsersIds, $userIds);

        // if user exist or not
        foreach ($userIds as $userId) {
            $record = SplitExpense::where('user_id', $userId)
                ->where('group_list_id', $data['groupId'])
                ->where('expense_id', $data['expenseId'])
                ->first();

            // var_dump($record);
            if ($record  === null) {
                $newUser = SplitExpense::create([
                    'user_id' => $userId,
                    'receiver_id' => Auth::user()->id,
                    'expense_id' => $data['expenseId'],
                    'group_list_id' => $data['groupId'],
                    'amount' => $splitExpense,
                    'status' => 'owe',
                    'is_Settled' => 'notSettled',
                ]);
            } else {
                $record->amount = $splitExpense;
                $record->save();
            }
        }
        // updating record for me
        $myRecord = SplitExpense::where('user_id', Auth::user()->id)
            ->where('group_list_id', $data['groupId'])
            ->where('expense_id', $data['expenseId'])
            ->first();
        $myRecord->amount = $splitExpense;
        $record->save();
        // update Expense
        $updateExpense = Expense::where('id', $data['expenseId'])->first();
        $updateExpense->title = $data['editTitle'];
        $updateExpense->amount = $data['editAmount'];
        $updateExpense->expense_date = $data['editExpenseDate'];
        $updateExpense->members = $members;
        $updateExpense->save();

        return response()->json(['updateExpense' => $data]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        // dd($id, 'delete expense');
        $deleteExpense = Expense::find($id);
        //returns true or false
        $deleted = Expense::find($id)->delete();
        SplitExpense::whereExpenseId($id)->delete();
        return  response()->json(['deleteExpense' => $deleteExpense]);
    }
}
