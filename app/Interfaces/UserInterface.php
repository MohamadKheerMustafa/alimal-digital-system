<?php

namespace App\Interfaces;

interface UserInterface
{
    public function all($request);
    public function get($id);
    public function store($request);
    public function update($request, $id);
    public function delete($id);
}