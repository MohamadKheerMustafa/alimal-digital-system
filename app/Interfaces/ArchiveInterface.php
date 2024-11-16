<?php

namespace App\Interfaces;

interface ArchiveInterface
{
    public function all($request);
    public function get($id);
    public function upload($request);
    public function download($request);
    public function update($request, $id);
    public function delete($id);

    public function askToUpdate($request);
    public function askToDelete($request);

    public function getAllRequests($request);
    public function handleStatusChanges($request);
}
