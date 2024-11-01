<?php

namespace App\Interfaces;

interface CategoryInterface
{
    public function all($request);
    public function get($id);
    public function store($request);
    public function update($request, $id);
    public function delete($id);
    public function getArchivesBasedOnCategory($request, $category_id);
}