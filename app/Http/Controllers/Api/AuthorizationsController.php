<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationRequest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Auth;
use JWTAuth;
use Socialite;

class AuthorizationsController extends Controller
{
    public function store($type, AuthorizationRequest $request)
    {
        if (!in_array($type, ['user', 'new_student'])) {
            return $this->response->errorBadRequest();
        }

        $credentials['password'] = $request->password;
        if ($type == 'user') {
            $username = $request->username;
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username :
                $credentials['phone'] = $username;
            if (!$token = Auth::guard('api')->attempt($credentials)) {
                return $this->response->errorUnauthorized('用户名或密码错误');
            }
        } else {
            $credentials['reg_num'] = $request->reg_num;
            if (!$token = Auth::guard('api_new_student')->attempt($credentials)) {
                return $this->response->errorUnauthorized('报名号或密码错误');
            }
        }
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if (!in_array($type, ['weixin'])) {
            return $this->response->errorBadRequest();
        }

        $driver = Socialite::driver($type);

        try {
            if ($code = $request->code) {
                $response = $driver->getAccessTokenResponse($code);
                $token = array_get($response, 'access_token');
            } else {
                $token = $request->access_token;

                if ($type == 'weixin') {
                    $driver->setOpenId($request->openid);
                }
            }

            $oauthUser = $driver->userFromToken($token);
        } catch (\Exception $e) {
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if ($unionid) {
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 没有用户，默认创建一个用户
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }
                break;
        }

        $token = Auth::guard('api')->fromUser($user);
        return $this->respondWithToken($token)->setStatusCode(201);
    }

    public function update()
    {
        if (JWTAuth::getPayload()['auth_type'] == 'user') {
            $token = Auth::guard('api')->refresh();
        } else {
            $token = Auth::guard('api_new_student')->refresh();
        }
        return $this->respondWithToken($token);
    }

    public function destroy()
    {
        if (JWTAuth::getPayload()['auth_type'] == 'user') {
            Auth::guard('api')->logout();
        } else {
            Auth::guard('api_new_student')->logout();
        }
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
