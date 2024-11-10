<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Interfaces\CategoryInterface;
use Illuminate\Http\Request;

class CategoryController extends AppBaseController
{
    public $categoryInterface;
    public function __construct(CategoryInterface $categoryInterface)
    {
        $this->middleware('auth:api');
        $this->categoryInterface = $categoryInterface;
        $this->middleware('permission:view_categories', ['only' => ['index', 'show', 'getArchivesBasedOnCategory']]);
        $this->middleware('permission:create_categories', ['only' => ['store']]);
        $this->middleware('permission:update_categories', ['only' => ['update']]);
        $this->middleware('permission:delete_categories', ['only' => ['destroy']]);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = $this->categoryInterface->all($request);

        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        $data = $this->categoryInterface->store($request);

        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $data = $this->categoryInterface->get($id);

        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $data = $this->categoryInterface->update($request, $id);

        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $data = $this->categoryInterface->delete($id);

        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function getArchivesBasedOnCategory(Request $request, $category_id)
    {
        $data = $this->categoryInterface->getArchivesBasedOnCategory($request, $category_id);

        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }
}
