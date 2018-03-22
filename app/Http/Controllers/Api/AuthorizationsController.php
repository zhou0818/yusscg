<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\WechatAuthorizationRequest;
use App\Models\NewStudent;
use App\Models\User;
use Auth;
use Cache;
use JWTAuth;

class AuthorizationsController extends Controller
{
    public function store($type, AuthorizationRequest $request)
    {
        if (!in_array($type, ['user', 'new_student'])) {
            return $this->response->errorBadRequest();
        }
        //获取账户数组
        $credentials = $this->generateCredentials($type, $request);

        //生成Token
        $token = $this->generateToken($type, $credentials);

        return $this->respondWithToken($token, $type)->setStatusCode(201);
    }

    public function wechatStore($type, WechatAuthorizationRequest $request)
    {
        $app = app('wechat.official_account');
        $oauth = $app->oauth;

        // 获取微信 OAuth 授权结果用户信息
        $user_wechat = $oauth->user();
        $openid = $user_wechat->getId();
        if ($type == 'user') {
            $user = User::where('weixin_openid', $openid)->first();
            //如果用户存在，返回 Token
            if ($user) {
                $token = Auth::guard('api')->fromUser($user);

                return $this->respondWithToken($token, $type)->setStatusCode(201);
            }
        } else {
            $user = NewStudent::where('weixin_openid', $openid)->first();
            if ($user) {
                $token = Auth::guard('api_new_student')->fromUser($user);

                return $this->respondWithToken($token, $type)->setStatusCode(201);
            }
        }
        $result = $this->generateOpenidKey($openid);

        return $this->response->array($result)->setStatusCode(201);
    }

    public function bind($type, WechatAuthorizationRequest $request)
    {
        //从缓存取出openid
        $openidData = Cache::get($request->openid_key);
        if (!$openidData) {
            return $this->response->error('验证期已经超时，请退出重进！', 422);
        }
        $token = $this->generateToken($type, $request);
        $credentials = $this->generateCredentials($type, $request);
        if ($type == 'user') {
            User::where($credentials)
                ->update(['weixin_openid' => $openidData['openid']]);
        } else {
            NewStudent::where($credentials)
                ->update(['weixin_openid' => $openidData['openid']]);
        }
        return $this->respondWithToken($token, $type)->setStatusCode(201);
    }

    public function update()
    {
        if (JWTAuth::getPayload()['auth_type'] == 'user') {
            $type = 'user';
            $token = Auth::guard('api')->refresh();
        } else {
            $type = 'new_student';
            $token = Auth::guard('api_new_student')->refresh();
        }
        return $this->respondWithToken($token, $type);
    }

    public function destroy()
    {
        if (JWTAuth::getPayload()['auth_type'] == 'user') {
            Auth::guard('api')->logout();
        } else {
            Auth::guard('api_new_student')->logout();
        }
        return $this->response->noContent();
    }

    protected function generateOpenidKey($openid)
    {
        $key = 'openid_' . str_random(15);
        $expiredAt = now()->addMinutes(30);
        Cache::put($key, ['openid' => $openid], $expiredAt);
        $result = [
            'openid_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ];
        return $result;
    }

    protected function respondWithToken($token, $type)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard($type)->factory()->getTTL() * 60
        ]);
    }

    protected function generateToken($type, $credentials)
    {
        if ($type == 'user') {
            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return $this->response->errorUnauthorized('用户名或密码错误');
            }
        } else {
            if (!$token = Auth::guard('api_new_student')->attempt($credentials)) {
                return $this->response->errorUnauthorized('报名号或密码错误');
            }
        }
        return $token;
    }

    protected function generateCredentials($type, $request)
    {
        $credentials['password'] = $request->password;
        if ($type == 'user') {
            $username = $request->username;
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username :
                $credentials['phone'] = $username;
        } else {
            $credentials['reg_num'] = $request->reg_num;
        }
        return $credentials;
    }
}
