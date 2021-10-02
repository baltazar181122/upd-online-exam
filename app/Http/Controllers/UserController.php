<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Auth;
class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function index(){
        //test
        $users = User::where('id', '<>', Auth::user()->id)->get();
        return view('admin.users.index', ['users' => $users]);
    }

    public function view(Request $request){
        $user = User::find($request->data);
        return response()->json($user);
    }

    public function save(Request $request){
        User::UpdateOrCreate(
            ['id' => $request->id],
            ['first_name' => $request->firstname,
            'last_name' =>  $request->lastname,
            'email' =>  $request->email,
            'batch' => $request->batch,
            'password' => $request->password]);
        return response()->json('success');
    }

}
