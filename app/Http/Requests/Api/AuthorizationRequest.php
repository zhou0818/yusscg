<?php

namespace App\Http\Requests\Api;

use Dingo\Api\Http\FormRequest;

class AuthorizationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

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
