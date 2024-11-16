<?php

namespace App\Http\Controllers;

use App\Interfaces\ArchiveInterface;
use Exception;
use Illuminate\Http\Request;

class ArchiveController extends AppBaseController
{
    public $archiveInterface;
    public function __construct(ArchiveInterface $archiveInterface)
    {
        $this->middleware('auth:api');
        $this->archiveInterface = $archiveInterface;
    }

    public function index(Request $request)
    {
        $data = $this->archiveInterface->all($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function get($id)
    {
        $data = $this->archiveInterface->get($id);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function upload(Request $request)
    {
        try {
            $data = $this->archiveInterface->upload($request);
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function download(Request $request)
    {
        try {
            $data = $this->archiveInterface->download($request);
            return $data;
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy($id)
    {
        $data = $this->archiveInterface->delete($id);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function getAllRequests(Request $request)
    {
        $data = $this->archiveInterface->getAllRequests($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function askToUpdate(Request $request)
    {
        $data = $this->archiveInterface->askToUpdate($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function askToDelete(Request $request)
    {
        $data = $this->archiveInterface->askToDelete($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }

    public function handleStatusChanges(Request $request)
    {
        $data = $this->archiveInterface->handleStatusChanges($request);
        return $this->handleResponse($data['statusCode'], $data['data'], $data['message']);
    }
}
