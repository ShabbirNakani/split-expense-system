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
        $authUserId = Auth::user()->id;
        // final logic
        // finding friends from group users
        $groupId = GroupUser::select('group_list_id')->whereUserId($authUserId)->get()->toArray();
        // dd($groupId);
        // finding groups
        $groupsWithUsers = GroupList::with('users')->whereIn('id', $groupId)->get()->toArray();
        // dd($groupsWithUsers);

        // finding friends
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
            // $expenses = SplitExpense::where([
            //     ['receiver_id', $friendId],
            //     ['user_id', $authUserId],
            // ])->orWhere([
            //     ['receiver_id', $authUserId],
            //     ['user_id', $friendId],
            // ])->where(['is_Settled' => 'notSettled'])
            //     ->get();

            // DB::enableQueryLog();
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

            // dd(DB::getQueryLog());
            $total_owe = 0;
            $total_pay = 0;

            // friend's id
            // dump($friendId);
            // dump($splitExpenses->toArray());
            foreach ($splitExpenses as $splitExpense) {
                // dump($splitExpense->toArray());
                // dump('owe condition =>', ($splitExpense->receiver_id == $authUserId) && ($splitExpense->user_id == $friendId));
                // dump('for owe expense id', $splitExpense->id);
                // dump('pay condition =>', ($splitExpense->receiver_id == $authUserId) && ($splitExpense->user_id == $friendId));
                // dump('pay for expense id', $splitExpense->id);

                // to filter my own split$splitExpenses
                // checking who spent and to whom
                // owe condition
                if (($splitExpense->receiver_id == $authUserId) && ($splitExpense->user_id == $friendId) && ($splitExpense->receiver_id !== $splitExpense->user_id)) {
                    // dump('owe amount =>', $splitExpense->amount);
                    $total_owe +=  $splitExpense->amount;
                } elseif (($splitExpense->receiver_id == $friendId) && ($splitExpense->user_id == $authUserId) && ($splitExpense->receiver_id !== $splitExpense->user_id)) {
                    // pay condition
                    // dump('pay amount =>', $splitExpense->amount);
                    $total_pay +=  $splitExpense->amount;
                }
                // else if (true) {
                // dd('stoped');
                // $friends[$friendId]['remainigAmount'] = 0;
                // $friends[$friendId]['status'] = 'No expense Shared';
                // }

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
                // dump('total_owe', $total_owe);
                // dump('total_pay', $total_pay);
            }
            // $friendname = $friend['name'];
            // dump("expense end for friend ,$friendname");
        }
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
        // dd($groupsWithExpenses);
        $data = [];
        foreach ($groupsWithExpenses as $group) {
            $total_owe = 0;
            $total_pay = 0;
            $remainingAmount = 0;
            // dump($group);
            if (count($group['expenses']) > 0) {
                # code...

                foreach ($group['expenses'] as $expense) {
                    # code...

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
                    // dump('total owe', $total_owe);
                    // dump('total pay', $total_pay);

                    // $data[$group['id']] = array();
                    // $data[$group['id']]['id'] = $group['id'];
                    // $data[$group['id']]['title'] = $group['title'];
                    // $data[$group['id']]['discription'] = $group['discription'];
                    // $data[$group['id']]['total_members'] = $group['total_members'];
                    // $data[$group['id']]['total_members'] = $group['total_members'];

                    if ($total_owe > $total_pay || $total_owe < $total_pay) {
                        $data[$group['id']] = array();
                        $data[$group['id']]['id'] = $group['id'];
                        $data[$group['id']]['title'] = $group['title'];
                        $data[$group['id']]['discription'] = $group['discription'];
                        $data[$group['id']]['total_members'] = $group['total_members'];
                        $data[$group['id']]['total_members'] = $group['total_members'];

                        if ($total_owe > $total_pay) {
                            $remainingAmount = $total_owe - $total_pay;
                            // dump('$remainingAmount owe:', $remainingAmount);

                            $data[$group['id']]['remainingAmount'] = ('+' . ($remainingAmount));
                            $data[$group['id']]['status'] = 'owe';
                        } elseif ($total_owe < $total_pay) {
                            $remainingAmount =  $total_pay - $total_owe;
                            // dump('$remainingAmount pay:', $remainingAmount);
                            $data[$group['id']]['remainingAmount'] = ('-' . ($remainingAmount));
                            $data[$group['id']]['status'] = 'pay';
                        }
                    }


                    // if ($total_owe > $total_pay) {
                    //     $remainingAmount = $total_owe - $total_pay;
                    //     // dump('$remainingAmount owe:', $remainingAmount);

                    //     $data[$group['id']]['remainingAmount'] = ('+' . ($remainingAmount));
                    //     $data[$group['id']]['status'] = 'owe';
                    // } elseif ($total_owe < $total_pay) {
                    //     $remainingAmount =  $total_pay - $total_owe;
                    //     // dump('$remainingAmount pay:', $remainingAmount);
                    //     $data[$group['id']]['remainingAmount'] = ('-' . ($remainingAmount));
                    //     $data[$group['id']]['status'] = 'pay';
                    // } elseif ($total_owe == $total_pay) {
                    //     $remainingAmount = 0;
                    //     $data[$group['id']]['remainingAmount'] = $remainingAmount;
                    //     $data[$group['id']]['status'] = 'default';
                    // }
                }
            }
            // dump('one group changed');
            // dump($data);
        }
        // dd($data);
        // exit();
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
        // DB::enableQueryLog();
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

        // dd(DB::getQueryLog());

        $total_owe = 0;
        $total_pay = 0;
        // remaining amount calculation
        foreach ($groupIds as $groupId) {
            // DB::enableQueryLog();
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
            // dd(DB::getQueryLog());
            // dump($splitExpenses);


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

        $updateData['remainingAmount'] = $remainingAmount;
        return response()->json($updateData);
    }
}
