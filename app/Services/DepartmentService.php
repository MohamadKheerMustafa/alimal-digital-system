<?php

namespace App\Services;

use App\ApiCode;
use App\Interfaces\DepartmentInterface;
use App\Interfaces\UserInterface;
use App\Models\HR\Department;

class DepartmentService implements DepartmentInterface
{

    public function all($request)
    {
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        $departments = Department::query()->when($search, fn($query) => $query->where('name', 'LIKE', "%$search%"))
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        return ['data' => $departments, 'message' => 'Here are all departments.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function get($id)
    {
        $department = Department::query()->findOrFail($id);

        return ['data' => $department, 'message' => 'Retrived Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function store($request)
    {
        // $user = Department::create([
        //     'name' => $request->name,
        //     'email' => $request->email,
        //     'password' => Hash::make($request->password)
        // ]);

        // return ['data' => UsersResource::make($user), 'message' => 'Created Successfully.', 'statusCode' => ApiCode::CREATED];
    }

    public function update($request, $id)
    {
        // $user = User::findOrFail($id);

        // if (isset($request->name) && $request->name != null)
        //     $user->name = $request->name;

        // if (isset($request->email) && $request->email != null)
        //     $user->email = $request->email;

        // if (isset($request->password) && $request->password != null)
        //     $user->password = Hash::make($request->password);

        // $user->save();

        // return ['data' => UsersResource::make($user), 'message' => 'Updated Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function delete($id)
    {
        // User::findOrFail($id)->delete();

        // return ['data' => null, 'message' => 'Deleted Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }
}
