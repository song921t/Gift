<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Auth;
use Mail;


class UsersController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth',[
            'except' => ['show', 'create', 'store', 'index', 'confirmEmail'],
        ]);
        $this->middleware('guest',[
            'only' => ['create'],
        ]);
    }

    //用户列表
    public function index()
    {
        //$users = User::all();
        $users = User::paginate(10); //分页
        return view('users.index', compact('users'));
    }

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

    //测试处理
    public function deal(Request $request){
        error_log(print_r($request),3,'deal.log');
    }

    //处理表单数据
    public function store(Request $request)
    {
        //error_log(print_r($request,1),3,'re.log');
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

        //Auth::login($user); //注册成功后自动登录
        $this->sendEmailConfirmationTo($user);
        //session()->flash('success', '你好，很高兴遇见你～');
        session()->flash('success', '验证邮件已发送到您的注册邮箱上，请注意查收。');
        //return redirect()->route('users.show', [$user]);
        return redirect('/');
    }

    //发送激活邮件
    protected function sendEmailConfirmationTo($user)
    {
        $view = 'emails.confirm';
        $data = compact('user');
        //$from = 'timioxc@yahoo.com';
        //$name = '921t';
        $to = $user->email;
        $subject = '感谢注册 Gift 应用！请确认你的邮箱。';
        Mail::send($view, $data, function ($message) use ($to, $subject) {
            $message->to($to)->subject($subject);
        });
    }

    //确认激活邮件
    public function confirmEmail($token)
    {
        $user = User::where('activation_token', $token)->firstOrFail();
        $user->activated = true;
        $user->activation_token = null;
        $user->save();

        Auth::login($user);
        session()->flash('success', '恭喜你，激活成功！');
        return redirect()->route('users.show', [$user]);
    }


    //编辑个人信息
    public function edit(User $user)
    {
        $this->authorize('update', $user); //使用授权策略
        return view('users.edit', compact('user'));
    }

    //编辑数据处理
    /**
     * @param User $user
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(User $user, Request $request)
    {
        $this->validate($request,[
            'name' => 'required|max:50',
            'password' => 'nullable|confirmed|min:6',
        ]);

        $this->authorize('update', $user);

        $data = [];
        $data['name'] = $request->name;
        if($request->password){
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);
        session()->flash('success', '个人资料更新成功！');

        return redirect()->route('users.show', $user->id);
    }

    /**
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $this->authorize('destroy',$user); //授权验证
        $user->delete();
        session()->flash('success', '删除成功！');
        return back();
    }


}
