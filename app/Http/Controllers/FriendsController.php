<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\GroupList;
use App\Models\GroupUser;
use App\Models\SplitExpense;
use App\Models\User;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PHPUnit\TextUI\XmlConfiguration\Group;
use Yajra\DataTables\Contracts\DataTable;
use Yajra\DataTables\DataTables as DataTablesDataTables;
use Yajra\DataTables\Facades\DataTables as FacadesDataTables;

class FriendsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $fGroups = GroupUser::with('groupOfFriend.users')->where('user_id', Auth::user()->id)->get()->toArray();
        // dd($fGroup);

        // dd($request->all());
        $authUserId = Auth::user()->id;
        // finding friends from group users
        $groupId = GroupUser::select('group_list_id')->whereUserId($authUserId)->get();
        // dd($groupId);
        // finding groups
        // DB::enableQueryLog();
        $groupsWithUsers = GroupList::with('users')->whereIn('id', $groupId)->get()->toArray();
        // finding friends
        // dd(DB::getQueryLog());
        dd($groupsWithUsers);
        $friends = [];
        foreach ($groupsWithUsers as $group) {
            // dump($group['users']);
            $users = $group['users'];
            foreach ($users as $user) {
                // dump($user);
                // find unique of users
                if (!isset($friends[$user['id']]) &&  ($user['id'] !== $authUserId)) {
                    $friends[$user['id']] = $user;
                }
            }
        }

        // finding expenses for each friend
        foreach ($friends as $friend) {
            $friendId = $friend['id'];
            // only expenses they share
            $splitExpenses = SplitExpense::where(function ($query) use ($friendId, $authUserId) {
                $query->where('user_id', $friendId)
                    ->where('receiver_id', $authUserId)
                    ->orWhere(function ($subquery) use ($friendId, $authUserId) {
                        $subquery->where('user_id', $authUserId)
                            ->where('receiver_id', $friendId);
                    });
            })
                ->where('is_Settled', '=', 'notSettled')
                ->get();
            $total_owe = 0;
            $total_pay = 0;

            // friend's id
            foreach ($splitExpenses as $splitExpense) {
                // to filter my own split$splitExpenses
                // checking who spent and to whom => owe condition
                if (($splitExpense->receiver_id == $authUserId) && ($splitExpense->user_id == $friendId)) {
                    $total_owe +=  $splitExpense->amount;
                } elseif (($splitExpense->receiver_id == $friendId) && ($splitExpense->user_id == $authUserId)) {
                    // pay condition
                    $total_pay +=  $splitExpense->amount;
                }
                // setting status and remaining amount
                if ($total_owe > $total_pay) {
                    $remainingAmount = $total_owe - $total_pay;
                    $friends[$friendId]['status'] = 'owe';
                    $friends[$friendId]['remainigAmount'] = ('+' . ($remainingAmount));
                } elseif ($total_owe < $total_pay) {
                    $remainingAmount =  $total_pay - $total_owe;
                    $friends[$friendId]['status'] = 'pay';
                    $friends[$friendId]['remainigAmount'] = ('-' . ($remainingAmount));
                } elseif ($total_owe == $total_pay) {
                    $remainingAmount = 0;
                    $friends[$friendId]['status'] = 'default';
                    $friends[$friendId]['remainigAmount'] =  ($remainingAmount);
                }
            }
        }

        // dd($groupsWithUsers);
        // dd($friends);
        return view('myFriends')->with(['friends' => $friends, 'friendsGroupsWithUsers' => $groupsWithUsers]);
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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    // to append data in the settle modal
    public function settelModalData(Request $request)
    {
        // dd($request->all());
        $friendId = $request->friendId;
        $authId = Auth::user()->id;
        $groupids = $request->groupids;
        // group with expenses
        $groupsWithExpenses = GroupList::with('expenses.splitExpenses')->whereIn('id', $groupids)->get()->toArray();
        $data = [];
        foreach ($groupsWithExpenses as $group) {
            $total_owe = 0;
            $total_pay = 0;
            $remainingAmount = 0;
            if (count($group['expenses']) > 0) {
                foreach ($group['expenses'] as $expense) {
                    foreach ($expense['split_expenses'] as $splitExpense) {
                        if ($splitExpense['receiver_id'] !== $splitExpense['user_id'] && $splitExpense['is_Settled'] == 'notSettled') {
                            // checking who spent and to whom
                            // owe condition
                            if (($splitExpense['receiver_id'] == $authId) && ($splitExpense['user_id'] == $friendId)) {
                                $total_owe += $splitExpense['amount'];
                            } else if (($splitExpense['receiver_id'] == $friendId) && ($splitExpense['user_id'] == $authId)) {
                                // pay condition
                                // dump('pay amount =>', $expens->amount);
                                $total_pay += $splitExpense['amount'];
                            }
                        }
                    }
                    if ($total_owe > $total_pay || $total_owe < $total_pay) {
                        $data[$group['id']] = array();
                        $data[$group['id']]['id'] = $group['id'];
                        $data[$group['id']]['title'] = $group['title'];
                        $data[$group['id']]['discription'] = $group['discription'];
                        $data[$group['id']]['total_members'] = $group['total_members'];
                        $data[$group['id']]['total_members'] = $group['total_members'];

                        if ($total_owe > $total_pay) {
                            $remainingAmount = $total_owe - $total_pay;
                            $data[$group['id']]['remainingAmount'] = ('+' . ($remainingAmount));
                            $data[$group['id']]['status'] = 'owe';
                        } elseif ($total_owe < $total_pay) {
                            $remainingAmount =  $total_pay - $total_owe;
                            $data[$group['id']]['remainingAmount'] = ('-' . ($remainingAmount));
                            $data[$group['id']]['status'] = 'pay';
                        }
                    }
                }
            }
        }
        return response()->json(['groupsWithExpenses' => $data, 'friendId' => $friendId]);
    }

    // to settel expenses
    public function settelExpense(Request $request)
    {
        // dd($request->all());
        $updateData = $request->all();
        $groupIds = $request->groups;
        $friendId = $request->friendId;
        $myId = Auth::user()->id;

        // settel those expenses which has the $myId as reciver_Id id and $friendId as user_id   or vive versa
        foreach ($groupIds as $groupId) {
            $splitExpenses = SplitExpense::whereGroupListId($groupId)
                ->where(function ($query) use ($friendId, $myId) {
                    $query->where('user_id', $friendId)
                        ->where('receiver_id', $myId)
                        ->orWhere(function ($subquery) use ($friendId, $myId) {
                            $subquery->where('user_id', $myId)
                                ->where('receiver_id', $friendId);
                        });
                })
                ->update(['is_Settled' => 'Settled']);
        }

        $total_owe = 0;
        $total_pay = 0;
        // remaining amount calculation
        foreach ($groupIds as $groupId) {
            $splitExpenses = SplitExpense::where(function ($query) use ($friendId, $myId) {
                $query->where('user_id', $friendId)
                    ->where('receiver_id', $myId)
                    ->orWhere(function ($subquery) use ($friendId, $myId) {
                        $subquery->where('user_id', $myId)
                            ->where('receiver_id', $friendId);
                    });
            })
                ->where('is_Settled', '=', 'notSettled')
                ->get();

            foreach ($splitExpenses as $splitExpense) {
                if (($splitExpense->receiver_id !== $splitExpense->user_id) && ($splitExpense->is_Settled == 'notSettled')) {
                    // checking who spent and to whom
                    // owe condition
                    if (($splitExpense->receiver_id == Auth::user()->id) && ($splitExpense->user_id == $friendId)) {
                        $total_owe += $splitExpense->amount;
                    } else if (($splitExpense->receiver_id == $friendId) && ($splitExpense->user_id == Auth::user()->id)) {
                        // pay condition
                        dump('pay amount =>', $splitExpense->amount);
                        $total_pay += $splitExpense->amount;
                    }
                }
            }
            // group end
        }
        if ($total_owe > $total_pay) {
            $remainingAmount = $total_owe - $total_pay;
        } elseif ($total_owe < $total_pay) {
            $remainingAmount =  $total_pay - $total_owe;
        } elseif ($total_owe == $total_pay) {
            $remainingAmount = 0;
        }

        $friendname = User::whereId($friendId)->first()->name;
        $updateData['remainingAmount'] = $remainingAmount;
        $updateData['friendName'] = $friendname;
        return response()->json($updateData);
    }
}
