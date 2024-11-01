<?php

namespace App\Http\Controllers;

use App\ApiCode;
use App\Http\Resources\Users\UsersResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends AppBaseController
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (!$token = JWTAuth::attempt($credentials)) {
            $data = ['data' => null, 'message' => 'Unauthorized', 'statusCode' => ApiCode::UNAUTHORIZED];
        }

        $user = auth()->user();
        $user->token = $token;

        Log::alert($request);
        $data = ['data' => UsersResource::make($user), 'message' => 'Logged in successfully', 'statusCode' => ApiCode::SUCCESS];
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return $this->handleResponse(ApiCode::SUCCESS, null, 'Successfully logged out');
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        $auth = auth()->refresh();
        $user = auth()->user();

        $user->token = $auth;

        $data = ['data' => UsersResource::make($user), 'message' => 'Logged in successfully', 'statusCode' => ApiCode::SUCCESS];
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }
}
