<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\TaskLock;

class TaskController extends Controller
{
    public function index()
    {
        $user = syncUserLevelAndLimit(auth()->user());
        $lockStatus = TaskLock::status($user);

        if ($lockStatus['locked']) {
            $pageTitle = 'Tasks Locked';
            return view($this->activeTemplate . 'user.task_locked', compact('pageTitle', 'lockStatus', 'user'));
        }

        $pageTitle = 'Task Room';
        return view($this->activeTemplate . 'user.task_room', compact('pageTitle', 'lockStatus'));
    }
}
