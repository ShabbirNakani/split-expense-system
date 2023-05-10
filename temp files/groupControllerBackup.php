<?php

namespace App\Http\Controllers;

use App\Models\GroupList;
use App\Models\GroupUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\Framework\Constraint\JsonMatches;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Psy\TabCompletion\Matcher\FunctionsMatcher;
use Symfony\Component\HttpFoundation\JsonResponse;

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

        // using relation how to featch the groups that a friend have added
        // need to define 2 realations

        //finding the group ids
        $groupsIdArray = GroupUser::whereUserId($userId)->select('group_list_id');
        $groups = GroupList::whereIn('id', $groupsIdArray)->orderBy($sort, $order)->paginate(10);

        if (isset($request->sort) & isset($request->order)) {
            $groups->withPath("/groups?sort=$sort&order=$order");
        }

        // searching functionlity if it has request
        $query = '';  // to pass null string if no query has been passed
        if ($request->has('query')) {
            $query = $request->input('query');
            if (empty($query)) {
                return redirect('groups')->with('status', 'No Valid Paramer To Search');
            }
            // Perform the search query
            $groups = GroupList::whereIn('id', $groupsIdArray)
                ->where('title', 'like', '%' . $query . '%')
                ->orWhere('discription', 'like', '%' . $query . '%')
                ->orWhere('total_members', 'like', '%' . $query . '%')
                ->orderBy($sort, $order)
                ->paginate(10);
        }

        // $query = '';  // to pass null string if no query has been passed
        // if ($request->has('query')) {
        //     $query = $request->input('query');
        //     if (empty($query)) {
        //         return redirect('groups')->with('status', 'No Valid Paramer To Search');
        //     }
        //     // Perform the search query
        //     $groups = GroupList::whereIn('id', $groupsIdArray)
        //         ->where(function ($query) {
        //             $query->where('title', 'like', '%' . $query . '%')
        //                 ->orWhere('discription', 'like', '%' . $query . '%')
        //                 ->orWhere('total_members', 'like', '%' . $query . '%');
        //         })->paginate(10);
        // }

        //not including me as i will make the payment => added my self in group users in store group
        $users = User::whereNotIn('id', [$userId])->get();

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
        dd($request->all());
        //  + 1 for myself
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

        return redirect('groups')->with('status', 'Group Created SuccesFully');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = GroupList::find($id);
        // dd($group->title);

        $groupUsers = User::select('users.*')
            ->join('group_users', 'group_users.user_id', '=', 'users.id')
            ->where('group_users.group_list_id', '=', "$id")
            ->where('users.id', '!=', Auth::user()->id)
            ->whereNull('group_users.deleted_at')
            ->get()->toArray();

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
        $creatorId = Auth::user()->id;
        $group = GroupList::find($id)->toArray();

        // how to not have my own record in this query
        // $groupUsers = GroupList::with('users')->where('id', $id)->get()->toArray();
        // dd($groupUsers);

        $groupUsers = User::select('users.*')
            ->join('group_users', 'group_users.user_id', '=', 'users.id')
            ->where('group_users.group_list_id', '=', "$id")
            ->where('users.id', '!=', "$creatorId")
            ->whereNull('group_users.deleted_at')
            ->get()->toArray();
        // dd($groupUsers);
        return response()->json(['group' => $group, 'groupUsers' => $groupUsers]);
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
        $userIds = $data['users'];

        // deleting removed users
        $currentUsersIds = GroupUser::where('group_list_id', $data['groupId'])->where('user_id', '!=', Auth::user()->id)->pluck('user_id')->toArray();
        // finding the diffrence
        $removedUsers = array_diff($currentUsersIds, $userIds);
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
        GroupList::find($id)->delete();
        GroupUser::whereGroupListId($id)->delete();
        return  redirect('groups')->with('status', 'Group deleted Sucessfully');
    }
}
