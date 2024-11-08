<?php

namespace App\Http\Controllers;

use App\ApiCode;
use App\Http\Resources\Users\UsersResource;
use Illuminate\Http\Request;
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
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken()
    {
        $user = auth()->user();

        if (!$user)
            return $this->handleResponse(ApiCode::UNAUTHORIZED, null, 'Unauthenticated');

        return $this->handleResponse(ApiCode::SUCCESS, UsersResource::make($user), 'Token Still Active.');
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
