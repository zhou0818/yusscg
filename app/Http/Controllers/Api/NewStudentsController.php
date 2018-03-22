<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NewStudentTransformer;
use Auth;

class NewStudentsController extends Controller
{
    public function me()
    {
        dd(Auth::guard('api_new_student')->user());
        return $this->response->item(Auth::guard('api_new_student')->user(), new NewStudentTransformer());
    }
}
