<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;


class UsersController extends Controller
{
    //注册页面
    public function create()
    {
        return view('users.create');
    }

    //个人信息页面
    public function show(User $user)
    {
        //echo '<pre>';print_r(compact('user'));die;
        return view('users.show', compact('user'));
    }

    //处理表单数据
    public function store(Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
            'email' => 'required|email|unique:users|max:255',
            'password' => 'required|confirmed|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        Auth::login($user); //注册成功后自动登录
        session()->flash('success', '你好，很高兴遇见你～');
        return redirect()->route('users.show', [$user]);
    }


}
