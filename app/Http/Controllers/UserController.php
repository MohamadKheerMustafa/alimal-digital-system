<?php

namespace App\Http\Controllers;

use App\Interfaces\UserInterface;
use Illuminate\Http\Request;

class UserController extends AppBaseController
{
    public $userInterface;
    public function __construct(UserInterface $userInterface)
    {
        $this->middleware('auth:api');
        $this->userInterface = $userInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->userInterface->all($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $this->userInterface->store($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->userInterface->get($id);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $this->userInterface->update($request, $id);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = $this->userInterface->delete($id);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }
}
