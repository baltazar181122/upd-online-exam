<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\BatchMasterlist;
use Illuminate\Http\Request;
// use App\User;
// use Illuminate\Http\Request;
use Auth;
use Illuminate\Auth\Events\Registered;
use DateTimeZone;
use DateTime;


class RegisterController extends Controller
{
    private $email;
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

    protected function redirectTo()
    {
        if (Auth::user()->user_type == 1) {
            return route('student');
        }else{
            return route('home');
        }
        // return $this->redirectTo; // or any route you want.
    }

    // protected $redirectTo = '/home';
   
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
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }




    public function showRegistrationForm($code = null)
    {
        $data = BatchMasterlist::where('code', $code)->first();
        if ($data->code) {
            $user = User::where('email', $data->email)->first();
            if ($user) {
                return redirect('/login')->with('exist','Please Login to Update your data!');
            }else{
                $this->email = $data->email;
                return view('auth.register',['email' => $data->email, 'code' => $data->code]);
            }
        }else{
            return 'Error Link, Back to Login page <a href="/login">Click Here</a>';
        }
    }

        /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $tz = 'Asia/Manila';
        $tz_obj = new DateTimeZone($tz);
        $today = new DateTime("now", $tz_obj);
        $code = BatchMasterlist::where('code', $data['code'])->first();
        return User::create([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' =>   $data['email'],
            'batch_id' => $code['batch_id'],
            'date_register' =>  $today->format('Y-m-d H:i:s'),
            'user_type' =>  1,
            'password' => Hash::make($data['password']),
        ]);
    }


    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
    }
    
}
