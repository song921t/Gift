<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $fillable = ['content'];
    /**
     * [user 一个微博属于一个用户]
     * @return [type] [description]
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
