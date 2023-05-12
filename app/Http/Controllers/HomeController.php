<?php

namespace App\Http\Controllers;

use App\Models\GroupUser;
use App\Models\SplitExpense;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $authUserId = Auth::user()->id;
        // dd($authUserId);
        $splitExpenses = SplitExpense::whereUserId($authUserId)
            ->orwhere('receiver_id', $authUserId)->get();
        $total_owe = 0;
        $total_pay = 0;
        foreach ($splitExpenses as $splitExpense) {
            if ($splitExpense->receiver_id == $authUserId) {
                $total_owe += $splitExpense->amount;
            } elseif ($splitExpense->user_id == $authUserId) {
                $total_pay += $splitExpense->amount;
            }
        }
        $totalOweRemaining = 0;
        $totalPayRemainig = 0;
        $splitExpensesForGrandTotal = SplitExpense::where('is_Settled', 'notSettled')
            ->where(function ($query) use ($authUserId) {
                $query->whereUserId($authUserId)
                    ->orwhere('receiver_id', $authUserId);
            })->get();

        // isset amount grand total like friends grand total
        foreach ($splitExpensesForGrandTotal as $splitExpenses) {
            if ($splitExpense->receiver_id == $authUserId) {
                $totalOweRemaining += $splitExpense->amount;
            } elseif ($splitExpense->user_id == $authUserId) {
                $totalPayRemainig += $splitExpense->amount;
            }
        }
        if ($totalOweRemaining > $totalPayRemainig) {
            $remainingAmount = '+' . ($totalOweRemaining - $totalPayRemainig);
        } elseif ($totalOweRemaining < $totalPayRemainig) {
            $remainingAmount =  '-' . $totalPayRemainig - $totalOweRemaining;
        } elseif ($totalOweRemaining == $totalPayRemainig) {
            $remainingAmount = 0;
        }
        // total groups
        $groupsCount = GroupUser::having('user_id', $authUserId)->count('user_id');
        // dd($groups);


        return view('home')->with(['total_owe' => $total_owe, 'total_pay' => $total_pay, 'remainingAmount' => $remainingAmount, 'groupsCount' => $groupsCount]);
    }


    public function updateProfile(Request $data)
    {
        $newdata = $data->toArray();
        $id = Auth::id();
        //regex for email
        $emailFormate = 'regex:/(.*)@(gmail|hotmail|webcodegenie|mailinator|yahoo)\.(com|ac.in|gov.in|net|in)/i';
        $email = Auth::user()->email;
        $newemail = $newdata['email'];

        if ($email != $newemail) {
            //validate the data
            $validated = $data->validate([
                'name' => ['required', 'regex:/^([a-zA-Z]+\s)*[a-zA-Z]+$/', 'max:255'],
                'email' => ['required', 'string', 'max:255', $emailFormate, 'unique:users'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'number' => ['required', 'min:8', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
                'Profilepic' => ['file', 'mimes:jpg,png,jpeg', 'between:100,10240'],
            ]);
        } else {
            //validate the data
            $validated = $data->validate([
                'name' => ['required', 'regex:/^([a-zA-Z]+\s)*[a-zA-Z]+$/', 'max:255'],
                'password' => ['nullable', 'string', 'min:8', 'confirmed'],
                'number' => ['required', 'min:8', 'regex:/^([0-9\s\-\+\(\)]*)$/'],
                'Profilepic' => ['file', 'mimes:jpg,png,jpeg', 'between:100,10240'],
            ]);
        }


        if (isset($newdata['Profilepic']) && isset($newdata['password'])) {

            //generate name for the image
            $filename = date('mdYHis') . uniqid() . $newdata['Profilepic']->getClientOriginalName();

            // pass that name and path into the profilepic in user create method
            $path = '../storage/app/public/imageUploads';

            //store the image into the storage/app/public/imageUploads
            $isuploaded = $newdata['Profilepic']->move($path, $filename);

            //delete the the previous image from the database
            $path = '../storage/app/public/imageUploads/' . Auth::user()->profile_pic;
            $isdeleted = File::delete($path);

            // update model
            $user = User::find($id);
            $user->name = $newdata['name'];
            $user->number = $newdata['number'];
            $user->email = $newdata['email'];
            $user->password = Hash::make($newdata['password']);
            $user->profile_pic = $filename;
            $user->save();
        } else if (isset($newdata['Profilepic']) && !isset($newdata['password'])) {

            //generate name for the image
            $filename = date('mdYHis') . uniqid() . $newdata['Profilepic']->getClientOriginalName();

            // pass that name and path into the profilepic in user create method
            $path = '../storage/app/public/imageUploads';

            //store the image into the storage/app/public/imageUploads
            $isuploaded = $newdata['Profilepic']->move($path, $filename);

            //delete the the previous image from the database
            $path = '../storage/app/public/imageUploads/' . Auth::user()->profile_pic;
            $isdeleted = File::delete($path);

            // update model
            $user = User::find($id);
            $user->name = $newdata['name'];
            $user->number = $newdata['number'];
            $user->email = $newdata['email'];
            $user->profile_pic = $filename;
            $user->save();
        } else if (!isset($newdata['Profilepic']) && isset($newdata['password'])) {

            // update model
            $user = User::find($id);
            $user->name = $newdata['name'];
            $user->number = $newdata['number'];
            $user->email = $newdata['email'];
            $user->password = Hash::make($newdata['password']);
            $user->save();
        } else {
            // update model
            $user = User::find($id);
            $user->name = $newdata['name'];
            $user->number = $newdata['number'];
            $user->email = $newdata['email'];
            $user->save();
        }

        return redirect()->to('editProfile')->with('status', 'Profile Updated Succesfully');
    }
}
