<?php

namespace App\Http\Requests\Api;


class AuthorizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'password' => 'required|string|min:6',
        ];
        if ($this->auth_type == 'user') {
            $rules['username'] = 'required|string';
        } else {
            $rules['reg_num'] = 'required|string';
        }
        return $rules;
    }
}
