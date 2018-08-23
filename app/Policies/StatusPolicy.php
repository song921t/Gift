<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Status;
use Illuminate\Auth\Access\HandlesAuthorization;

class StatusPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * [destroy 删除微博]
     * @param  User   $user   [用户]
     * @param  Status $status [微博]
     * @return [type]         [description]
     */
    public function destroy(User $user, Status $status)
    {
        return $user->id === $status->user_id;
    }

}
