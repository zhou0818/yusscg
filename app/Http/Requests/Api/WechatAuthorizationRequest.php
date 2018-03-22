<?php

namespace App\Http\Requests\Api;


class WechatAuthorizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        switch ($this->method()) {
            case 'PUT':
                $rules = [
                    'password' => 'required|string|min:6',
                    'openid_key' => 'required|string',
                ];
                if ($this->auth_type == 'user') {
                    $rules['username'] = 'required|string';
                } else {
                    $rules['reg_num'] = 'required|string';
                }
                return $rules;
                break;
            default:
                return [
                    'code' => 'required|string',
                ];
                break;
        }
    }
}
