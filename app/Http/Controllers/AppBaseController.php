<?php

namespace App\Http\Controllers;

use App\ApiCode;
use Illuminate\Support\Facades\Response;

class AppBaseController extends Controller
{
    public function sendResponse($result, $message,  $code = 200, $errorCode = 0)
    {
        return Response::json([
            'status' => $code,
            'errorCode' => $errorCode,
            'data' => $result,
            'message' => $message
        ], $code);
    }

    public function handleResponse($statusCode, $data = null,  $message)
    {
        switch ($statusCode) {
            case 200:
                return $this->sendResponse(
                    $data,
                    __($message),
                    ApiCode::SUCCESS,
                    0
                );
                break;
            case 201:
                return $this->sendResponse(
                    $data,
                    __($message),
                    ApiCode::CREATED,
                    0
                );
                break;
            case 400:
                return $this->sendResponse(
                    null,
                    __($message),
                    ApiCode::BAD_REQUEST,
                    1
                );
                break;
            case 403:
                return $this->sendResponse(
                    null,
                    !empty($message) ? __($message) : __('User does not have any of the necessary access rights.'),
                    ApiCode::FORBIDDEN,
                    1
                );
                break;
            case 404:
                return $this->sendResponse(
                    null,
                    __($message),
                    ApiCode::NOT_FOUND,
                    1
                );
                break;
            default:
                return $this->sendResponse(
                    null,
                    $message,
                    ApiCode::SOMETHING_WENT_WRONG,
                    1
                );
                break;
        }
    }
}
