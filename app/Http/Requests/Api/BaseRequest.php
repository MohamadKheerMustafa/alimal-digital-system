<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Http\Controllers\AppBaseController;
use App\ApiCode;
use Illuminate\Contracts\Validation\Validator;

class BaseRequest extends FormRequest
{
    private AppBaseController $appBaseController;
    public function __construct(AppBaseController $appBaseController)
    {
        $this->appBaseController = $appBaseController;
    }

    protected function failedValidation(Validator $validator)
    {
        $response = $this->appBaseController->sendResponse(
            $validator->errors(),
            __("validation error!"),
            ApiCode::BAD_REQUEST,
            1
        );

        throw new \Illuminate\Validation\ValidationException($validator, $response);
    }
}
