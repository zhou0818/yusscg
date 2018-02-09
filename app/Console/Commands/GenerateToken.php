<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use App\Models\NewStudent;

class GenerateToken extends Command
{
    protected $signature = 'larabbs:generate-token';

    protected $description = '快速为用户生成 token';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $type = $this->choice('请选择用户类型', ['user', 'new_student']);
        $userId = $this->ask('输入用户 id');
        if ($type == 'user') {
            $user = User::find($userId);
            $this->generateToken($user, 'api');
        } else {
            $user = NewStudent::find($userId);
            $this->generateToken($user, 'api_new_student');
        }


    }

    protected function generateToken($user, $guard)
    {
        if (!$user) {
            return $this->error('用户不存在');
        }
        // 一年以后过期
        $ttl = 365 * 24 * 60;
        $this->info(\Auth::guard($guard)->setTTL($ttl)->fromUser($user));
    }
}
