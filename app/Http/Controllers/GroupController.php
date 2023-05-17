<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\GroupList;
use App\Models\GroupUser;
use App\Models\SplitExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $sort = isset($request->sort) ? $request->sort : 'created_at';
        $order = isset($request->order) ? $request->order : 'asc';
        $userId =  Auth::user()->id;

        $groupQuery = GroupList::select('group_lists.*')
            ->join('group_users', 'group_lists.id', '=', 'group_users.group_list_id')
            ->join('users', 'users.id', '=', 'group_lists.user_id')
            ->where('group_users.user_id', '=', $userId)
            ->orderBy("group_lists.$sort", $order);
        // dd($groupQuery->get());

        // $groupQuery = GroupList::with('myGroups')->orderBy($sort, $order);

        // searching functionlity if it has request
        $query = '';  // to pass null string if no query has been passed
        if ($request->has('query')) {
            $query = $request->input('query');
            // if (empty($query)) {
            //     return redirect('groups')->with('status', 'No Valid Paramer To Search');
            // }
            // Perform the search query
            $groupQuery->where(function ($subQuery) use ($query) {
                $subQuery->where('group_lists.title', 'like', '%' . $query . '%')
                    ->orWhere('group_lists.discription', 'like', '%' . $query . '%')
                    ->orWhere('group_lists.total_members', 'like', '%' . $query . '%');
            });
        }

        $groups = $groupQuery->paginate(10);
        // // if the page is sorted from the another page except 1st ,then only that page should load
        // if (isset($request->sort) & isset($request->order)) {
        //     $groups->withPath("/groups?sort=$sort&order=$order");
        // }

        //not including me as i will make the payment => added my self in group users in store group
        $users = User::where('id', '!=', $userId)->get();
        // dd($users);
        return view('myGroups')->with(['groups' => $groups, 'users' => $users, 'query' => $query]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        // creare group view that does not exist in our project
        // do not user this route
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request);
        // if ($validation->fails()) {
        //     return redirect()->back()->withErrors($validation->errors());
        // }
        // dd($request);
        //  + 1 for myself
        try {
            $totalMembers = sizeof($request->users) + 1;
            $newGroup = GroupList::create([
                'user_id' => Auth::user()->id,
                'title' => $request->title,
                'discription' => $request->discription,
                'total_members' => $totalMembers,
            ]);
            // store groupusers
            foreach ($request->users as $userId) {
                GroupUser::create([
                    'user_id' => $userId,
                    'group_list_id' => $newGroup->id,
                ]);
            }
            // creatin my record
            GroupUser::create([
                'user_id' => Auth::user()->id,
                'group_list_id' => $newGroup->id,
            ]);

            return redirect('/groups')->with('status', 'Group Created SuccesFully');
        } catch (\Exception $e) {
            return redirect('/groups')->with('status', 'Some technical error occured!!!');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // dd($id);
        $group = GroupList::find($id);
        // dd($group->title);
        // DB::enableQueryLog();
        $groupUsers = User::select('users.*')
            ->join('group_users', 'group_users.user_id', '=', 'users.id')
            ->where('group_users.group_list_id', '=', "$id")
            ->where('users.id', '!=', Auth::user()->id)
            ->whereNull('group_users.deleted_at')
            ->get()->toArray();
        // dd(DB::getQueryLog());
        // to find unique
        $groupUsersId = array_unique(array_column($groupUsers, 'id'));
        $groupUsers = User::whereIn('id', $groupUsersId)->get();
        // dd($groupUsers);
        return view('showGroupView')->with(['group' => $group, 'groupUsers' => $groupUsers]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        // $validator = JsValidator::make($this->validationRules);
        // auth user
        // groupcreator from query
        // DB::enableQueryLog();
        $requester = Auth::user()->id;
        $groupCreatorId = GroupList::whereId($id)->first()->user_id;
        if ($requester == $groupCreatorId) {
            $groupWithUsers = GroupList::with('groupUsers.groupUsers')->whereId($id)->get();
            return response()->json(['groupWithUsers' => $groupWithUsers]);
        } else {
            // return response()->json(['message' => 'You are not allowed to edit This Group']);
            return redirect('groups')->with('status', 'You are not allowed to delete this Group');
        }
        // dd(DB::getQueryLog());
        // dd($groupWithUsers->toArray());
        // dd($groupWithUsers);
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
        // dd($data);
        $userIds = $data['users'];

        // deleting removed users
        $currentUsersIds = GroupUser::where('group_list_id', $data['groupId'])
            ->where('user_id', '!=', Auth::user()->id)
            ->pluck('user_id')
            ->toArray();
        // finding the diffrence
        $removedUsers = array_diff($currentUsersIds, $userIds);
        // dd($removedUsers);
        // exit();
        $deleted = GroupUser::whereGroupListId($data['groupId'])->WhereIn('user_id', $removedUsers)->delete();

        // create users
        foreach ($userIds as $userId) {
            GroupUser::firstOrCreate([
                'user_id' => $userId,
                'group_list_id' => $data['groupId'],
            ]);
        }

        // update other data =>  + 1 for myself
        $totalMembers = sizeof($userIds) + 1;
        $group = GroupList::where('id', $data['groupId'])->first();
        $group->title = $data['title'];
        $group->discription = $data['discription'];
        $group->total_members = $totalMembers;
        $group->save();
        return  redirect('groups')->with('status', 'Group Details Updated Sucessfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        // dd($id, 'delete');

        $requester = Auth::user()->id;
        // dump($requester);
        $groupCreatorId = GroupList::whereId($id)->first()->user_id;
        // dd($groupCreatorId);

        if ($requester == $groupCreatorId) {
            $groupExpenses = GroupList::with('expenses.splitExpenses')->whereId($id)->first();
            $remainigSettelemente = 0;
            foreach ($groupExpenses->expenses as $expense) {
                // dump($expense->splitExpenses);
                foreach ($expense->splitExpenses as $splitExpense) {
                    // dump($splitExpense->is_Settled);
                    if ($splitExpense->is_Settled == "notSettled") {
                        $remainigSettelemente += 1;
                    }
                }
            }
            // dd($remainigSettelemente);
            if (!$remainigSettelemente) {
                GroupList::find($id)->delete();
                GroupUser::whereGroupListId($id)->delete();
                Expense::whereGroupListId($id)->delete();
                SplitExpense::whereGroupListId($id)->delete();
                return  redirect('groups')->with('status', 'Group deleted Sucessfully');
            } else {
                return  redirect('groups')->with('status', 'Can not Delete Group,Settelment is Remaining');
            }
        } else {
            return redirect('groups')->with('status', 'You are not allowed to delete this Group');
        }
    }


    public function checkExpensesBeforeEdit(Request $request)
    {
        // dd($request->all());

        // featching user ids from modal
        $userIdsFromEditModal = $request->groupUsers;
        $groupHasChangedUser = false;
        $isvalid = true;
        // $previousGroupUsers = GroupList::with('groupUsers.groupUsers')->whereId($request->groupId)->get()->toArray();
        // dd($previousGroupUsers);

        // featching current users
        $currentUserIds = GroupUser::where('group_list_id', $request->groupId)
            ->where('user_id', '!=', Auth::user()->id)
            ->pluck('user_id')
            ->toArray();

        // checking if any user have been removed or not
        $removedUsers = array_diff($currentUserIds, $userIdsFromEditModal);
        count($removedUsers) > 0  ? $groupHasChangedUser = true : $groupHasChangedUser = false;

        if ($groupHasChangedUser) {
            $groupExpenses = SplitExpense::where('group_list_id', '=', $request->groupId)->get()->toArray();
            # code...
            if (count($groupExpenses) > 0) {
                $isvalid = false;
            } else {
                $isvalid = true;
            }
            return response()->json(['isvalid' => $isvalid]);
        } else {
            return response()->json(['isvalid' => $isvalid]);
        }

        // return count($groupExpenses) > 0;
    }
}
