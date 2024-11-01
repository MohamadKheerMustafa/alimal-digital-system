<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Users\UsersCollection;
use App\Http\Resources\Users\UsersResource;
use App\Interfaces\UserInterface;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Hash;

class UserService implements UserInterface
{
    use UserTrait;

    public function all($request)
    {
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        $users = User::query()->when($search, fn($query) => $query->where('name', 'LIKE', "%$search%"))
            ->latest()
            ->paginate($limit, ['*'], 'page', $page);

        return ['data' => new UsersCollection($users), 'message' => 'Here are all users.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function get($id)
    {
        $user = User::query()->findOrFail($id);

        return ['data' => UsersResource::make($user), 'message' => 'Retrived Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function store($request)
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
        ]);

        if ($user) {
            $this->createProfile([
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'contact_number' => $request->contact_number,
                'address' => $request->address
            ]);
        }

        return ['data' => UsersResource::make($user), 'message' => 'Created Successfully.', 'statusCode' => ApiCode::CREATED];
    }

    public function update($request, $id)
    {
        $user = User::findOrFail($id);

        if (isset($request->name) && $request->name != null)
            $user->name = $request->name;

        if (isset($request->email) && $request->email != null)
            $user->email = $request->email;

        if (isset($request->password) && $request->password != null)
            $user->password = Hash::make($request->password);

        $user->save();

        return ['data' => UsersResource::make($user), 'message' => 'Updated Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return ['data' => null, 'message' => 'Deleted Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }
}
