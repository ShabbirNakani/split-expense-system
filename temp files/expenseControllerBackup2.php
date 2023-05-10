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

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        //finding the group ids
        $groupId = $request->groupId;
        $expensesIdArray = SplitExpense::whereGroupListId($groupId)->select('expense_id');

        // building an query object
        $query = Expense::whereIn('id', $expensesIdArray)->orderBy('created_at', 'asc');

        // search expseses (live search)
        if ($request->has('search')) {
            // live search logic
            if (!empty($request->search)) {
                $query->whereGroupListId($groupId)
                    ->where('title', 'LIKE', '%' . $request->search . "%")
                    ->orWhere('amount', 'LIKE', '%' . $request->search . "%")
                    ->orWhere('expense_date', 'LIKE', '%' . $request->search . "%")
                    ->orWhere('members', 'LIKE', '%' . $request->search . "%");
            }
        }

        // user filter and date range filter
        if ($request->has('dateRange') || $request->has('filterUser')) {
            if ($request->dateRange) {
                $range =  explode(' - ', $request->dateRange);
                $startDate = \Carbon\Carbon::parse($range[0])->format('Y-m-d H:i:s');
                $endDate = \Carbon\Carbon::parse($range[1])->format('Y-m-d H:i:s');
                $query->whereBetween('expense_date', [$startDate, $endDate]);
            }
            if ($request->filterUser !== 'null') {
                $query->whereUserId($request->filterUser)->whereGroupListId($request->groupId);
            }
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
        $creatorId = Auth::user()->id;
        $expense = Expense::find($id)->toArray();

        $expenseUsers = User::select('users.*')
            ->join('split_expenses', 'split_expenses.user_id', '=', 'users.id')
            ->where('split_expenses.expense_id', '=', "$id")
            ->where('users.id', '!=', "$creatorId")
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
        // dd($request->input());
        $data = $request->input();

        // delete group users to save the new updated users
        $userIds = $data['editExpenseUsers'];
        $members = count($data['editExpenseUsers']) + 1;
        $splitExpense = number_format((float)$data['editAmount'] / $members, 2, '.', '');

        // deleting removed users
        $currentUsersIds = SplitExpense::where('group_list_id', $data['groupId'])
            ->where('user_id', '!=', Auth::user()->id)
            ->where('expense_id', $data['expenseId'])
            ->pluck('user_id')
            ->toArray();
        // finding the diffrence
        $removedUsers = array_diff($currentUsersIds, $userIds);
        // deleting removed users
        $deleted = SplitExpense::whereGroupListId($data['groupId'])
            ->where('expense_id', $data['expenseId'])
            ->WhereIn('user_id', $removedUsers)
            ->delete();

        //creating splitexpense
        foreach ($userIds as $userId) {
            $splitExpense = SplitExpense::firstOrCreate([
                'user_id' => $userId,
                'receiver_id' => Auth::user()->id,
                'expense_id' => $data['expenseId'],
                'group_list_id' => $data['groupId'],
                'amount' => $splitExpense,
                'status' => 'owe',
                'is_Settled' => 'notSettled',
            ]);

            // $splitExpense->amount = $splitExpense;
            // $splitExpense->save();
        }


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
        $deleted = Expense::find($id)->delete();
        SplitExpense::whereExpenseId($id)->delete();
        return  response()->json(['deleteExpense' => $deleteExpense]);
    }
}
