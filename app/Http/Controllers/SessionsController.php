<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class SessionsController extends Controller
{
    public function __construct()
    {
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

    //登录页面
    public function create()
    {
        return view('sessions.create');
    }

    //数据处理
    public function store(Request $request)
    {
        $credentials = $this->validate($request,[
            'email' => 'required|email|max:255',
            'password' => 'required',
        ]);
        //error_log(print_r($credentials,1),3,'cre.log');

        if(Auth::attempt($credentials,$request->has('remember'))){
            //激活邮件
            if (Auth::user()->activated) {
                session()->flash('success', '欢迎回来！');
                return redirect()->intended(route('users.show', [Auth::user()]));
            } else {
                Auth::logout();
                session()->flash('warning', '您的账户未激活，请检查邮箱中的注册邮件进行激活。');
                return redirect('/');
            }
        }else{
            session()->flash('danger', '很抱歉，您的邮箱和密码不匹配');
            return redirect()->back();
        }
    }

    //退出
    public function destroy()
    {
        Auth::logout();
        session()->flash('success', '您以退出成功！');
        return redirect('login');
    }


}
