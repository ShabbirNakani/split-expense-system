<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\GroupList;
use App\Models\GroupUser;
use App\Models\SplitExpense;
use App\Models\User;
use App\Utils\Paginate;
use Illuminate\Http\Request;
use DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
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
        // for groupWise filter
        $friendsGroups = GroupUser::with('groupOfFriend.users')->where('user_id', Auth::user()->id)->get()->toArray();
        // dd($friendsGroups);
        $authUserId = Auth::user()->id;
        // finding friends from group users
        $groupId = GroupUser::select('group_list_id')->whereUserId($authUserId);
        // finding groups
        $friendsGroupsWithUsers = GroupList::with('users')->whereIn('id', $groupId)->get()->toArray();
        // finding friends
        $friends = [];
        foreach ($friendsGroupsWithUsers as $group) {
            $users = $group['users'];
            foreach ($users as $user) {
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

            if (count($splitExpenses) > 0) {
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
            } else {
                $remainingAmount = 0;
                $friends[$friendId]['status'] = 'default';
                $friends[$friendId]['remainigAmount'] =  ($remainingAmount);
            }
            // to add friends groups to friends array
            foreach ($friendsGroupsWithUsers as $groups) {
                foreach ($groups['users'] as $users) {
                    if ($users['id'] == $friend['id']) {
                        $friends[$friendId]['groups'][$groups['id']] = $groups['title'];
                    }
                }
            }
        }

        // code to render blade using ajax
        // if ($request->ajax()) {
        //     $returnHTML = view('renderSettelExpenseTable', compact(['friends', 'friendsGroupsWithUsers']))->render();
        //     return response()->json(['success' => true, 'html' => $returnHTML]);
        // } else {
        //     return view('myFriends')->with('friendsGroups', $friendsGroups);
        // }
        // dd($friends);

        // converting the array into collection
        $friendsObject = collect($friends)->map(function ($friend) {
            return (object) $friend;
        });
        // dd($friendsObject);
        if ($request->ajax()) {
            // $data = User::select('id', 'name', 'email')->get();
            return DataTablesDataTables::of($friendsObject)->addIndexColumn()
                ->addColumn('action', function ($row) {
                    // $btn = '<a href="javascript:void(0)" class="btn btn-primary btn-sm">View</a>';
                    $btn = "<button name='settle' value='Settle' class='btn  mr-2  text-primary open-settel-modal'
                        data-friend-id='" . $row->id . "'>
                        <i class='fa fa-money' style='font-size:24px'></i>
                    </button>";
                    return $btn;
                })
                ->addColumn('groupNames', function ($row) {
                    $elemets = "<div class='row'>";
                    // dd($row->groups);
                    foreach ($row->groups as $key => $groups) {
                        $elemets .=
                            "<div class='mr-3' data-group-id='" . $key . "'>
                               <h4>
                                    <span class='badge badge-light'> " . $groups  . "</span>
                                </h4>
                            </div>";
                    }
                    $elemets .= "</div>";
                    return  $elemets;
                })
                // ->escape(false)
                ->rawColumns(['action', 'groupNames'])
                ->make(true);
        }
        return view('myFriends')->with('friendsGroups', $friendsGroups);
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
