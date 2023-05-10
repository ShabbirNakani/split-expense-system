<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Unique;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\File;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */


    protected function validator(array $data)
    {
        // dd($data);
        // $image = $data["Profilepic"];
        // Build the input for validation
        // $fileArray = array('image' => $image);
        // dd($fileArray);

        $messages = [
            'email.regex' => 'This Domain name is invalid, please provide a valid Domain name',
            'Profilepic.between' => 'Profile Pic size must be more than 100kb or less then 10mb.'
        ];
        $emailFormate = 'regex:/(.*)@(gmail|hotmail|webcodegenie|mailinator|yahoo)\.(com|ac.in|gov.in|net)/i';

        return Validator::make($data, [
            'name' => ['required', 'regex:/^([a-zA-Z]+\s)*[a-zA-Z]+$/', 'max:255'],
            'email' => ['required', 'string', 'max:255', 'unique:users', $emailFormate],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'number' => ['required', 'min:8', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'unique:users'],
            'Profilepic' => ['file', 'mimes:jpg,png,jpeg', 'between:100,10240'],
        ], $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        // dd(isset($data['Profilepic']));

        if (isset($data['Profilepic'])) {

            //generate name for the image
            $filename = date('mdYHis') . uniqid() . $data['Profilepic']->getClientOriginalName();

            // pass that name and path into the profilepic in user create method
            $path = '../storage/app/public/imageUploads';

            //store the image into the storage/app/public/imageUploads
            $isuploaded = $data['Profilepic']->move($path, $filename);
            // dd($isuploaded);

            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'number' => $data['number'],
                'profile_pic' => $filename,
            ]);
        } else {
            return User::create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'number' => $data['number'],
            ]);
        }
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        return redirect()->to('/login');
    }
}
