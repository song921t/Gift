<?php

namespace App\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\ResetPassword;
use Auth;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public static function boot()
    {
        parent::boot();
        static::creating(function ($user) {
            $user->activation_token = str_random(30);
        });
    }

    //gravatar头像
    public function gravatar($size='100')
    {
        $hash = md5(strtolower(trim($this->attributes['email'])));
        return "http://www.gravatar.com/avatar/$hash?s=$size";
    }

    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPassword($token));
    }

    /**
     * [statuses 一个用户拥有多条微博]
     * @return [type] [description]
     */
    public function statuses()
    {
        return $this->hasMany(Status::class);
    }

    /**
     * [feed 用户微博数据]
     * @return [type] [description]
     */
    public function feed()
    {
        $user_ids = Auth::user()->followings->pluck('id')->toArray();
        array_push($user_ids, Auth::user()->id);
        return Status::whereIn('user_id', $user_ids)->with('user')->orderBy('created_at', 'desc');
        // return $this->statuses()->orderBy('created_at', 'desc');
    }

    /**
     * [followers 获取粉丝数据]
     * @return [type] [description]
     */
    public function followers()
    {
        return $this->belongsToMany(User::Class, 'followers', 'user_id', 'follower_id');
    }

    /**
     * [followings 获取关注人数据]
     * @return [type] [description]
     */
    public function followings()
    {
        return $this->belongsToMany(User::Class, 'followers', 'follower_id', 'user_id');
    }

    /**
     * [follow 关注]
     * @param  [type] $user_ids [description]
     * @return [type]           [description]
     */
    public function follow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->sync($user_ids, false);
    }

    /**
     * [unfollow 取消关注]
     * @param  [type] $user_ids [description]
     * @return [type]           [description]
     */
    public function unfollow($user_ids)
    {
        if (!is_array($user_ids)) {
            $user_ids = compact('user_ids');
        }
        $this->followings()->detach($user_ids);
    }

    /**
     * [isFollowing 是否关注]
     * @param  [type]  $user_id [description]
     * @return boolean          [description]
     */
    public function isFollowing($user_id)
    {
        return $this->followings->contains($user_id);
    }

}
