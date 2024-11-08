<?php

namespace App\Http\Controllers;

use App\Interfaces\DepartmentInterface;
use Illuminate\Http\Request;

class DepartmentController extends AppBaseController
{
    public $departmentInterface;

    public function __construct(DepartmentInterface $departmentInterface)
    {
        $this->middleware('auth:api');
        $this->departmentInterface = $departmentInterface;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->departmentInterface->all($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->departmentInterface->get($id);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
