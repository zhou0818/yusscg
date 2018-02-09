<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\UserRequest;
use App\Models\User;
use App\Transformers\UserTransformer;
use Auth;
use Cache;

class UsersController extends Controller
{
    public function store(UserRequest $request)
    {
        $verifyData = Cache::get($request->verification_key);

        if (!$verifyData) {
            return $this->response->error('验证码已失效', 422);
        }

        if (!hash_equals($verifyData['code'], $request->verification_code)) {
            // 返回401
            return $this->response->errorUnauthorized('验证码错误');
        }

        $user = User::where('phone', $verifyData['phone'])->first();
        $user->is_active = true;
        $user->save();

        // 清除验证码缓存
        Cache::forget($request->verification_key);

        return $this->response->created();
    }

    public function me()
    {
        return $this->response->item(Auth::guard('api')->user(), new UserTransformer());
    }
}

