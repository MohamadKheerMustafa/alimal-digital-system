<?php

namespace App\Services;

use App\ApiCode;
use App\Http\Resources\Profiles\ProfilesResource;
use App\Http\Resources\Users\UsersCollection;
use App\Http\Resources\Users\UsersResource;
use App\Interfaces\UserInterface;
use App\Models\Profile;
use App\Models\User;
use App\Traits\UserTrait;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserService implements UserInterface
{
    use UserTrait;

    public function all($request)
    {
        $limit = $request->query('limit', 12);
        $page = $request->query('page', 1);
        $search = $request->query('search', null);

        $users = User::query()->when($search, fn($query) => $query->where('name', 'LIKE', "%$search%"))
            ->latest('id')
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
            $profileData = [
                'user_id' => $user->id,
                'department_id' => $request->department_id,
                'position' => $request->position,
                'contact_number' => $request->contact_number,
                'address' => $request->address,
            ];

            // Handle image upload
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('/uploads/profile_images', 'public');
                $profileData['image'] = $imagePath;  // Save path to the profile data
            }

            $this->createProfile($profileData);
        }

        return ['data' => UsersResource::make($user), 'message' => 'Created Successfully.', 'statusCode' => ApiCode::CREATED];
    }

    public function update($request, $id)
    {
        $user = User::findOrFail($id);
        $profile = $user->profile;

        // Update basic user fields
        if (isset($request->name) && $request->name != null) {
            $user->name = $request->name;
        }

        if (isset($request->email) && $request->email != null) {
            $user->email = $request->email;
        }

        if (isset($request->password) && $request->password != null) {
            $user->password = Hash::make($request->password);
        }

        // Update profile fields
        if (isset($request->contact_number) && $request->contact_number != null) {
            $profile->contact_number = $request->contact_number;
        }

        if (isset($request->address) && $request->address != null) {
            $profile->address = $request->address;
        }

        // Check if image file is present in the request
        if ($request->hasFile('image')) {
            // Delete the old image if it exists and does not contain "avatars" in the path
            if ($profile->image && !str_contains($profile->image, 'avatars')) {
                Storage::disk('public')->delete($profile->image);
            }

            // Store the new image
            $path = $request->file('image')->store('uploads/profile_images', 'public');
            $profile->image = $path;
        }

        // Save user and profile changes
        $user->save();
        $profile->save();

        return [
            'data' => UsersResource::make($user),
            'message' => 'Updated Successfully.',
            'statusCode' => ApiCode::SUCCESS
        ];
    }

    public function delete($id)
    {
        User::findOrFail($id)->delete();

        return ['data' => null, 'message' => 'Deleted Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }

    public function userProfile()
    {
        return ['data' => ProfilesResource::make(auth()->user()->profile), 'message' => 'Here is your profile info!!', 'statusCode' => ApiCode::SUCCESS];
    }

    public function updateProfile($request, $id)
    {
        $user = Profile::findOrFail($id);

        if (isset($request->contact_number) && $request->contact_number != null)
            $user->contact_number = $request->contact_number;

        if (isset($request->address) && $request->address != null)
            $user->address = $request->address;

        // if ($request->file('image')) {
        // Get the uploaded file
        $image = $request->profile_image;

        // // Create a filename based on the user's name, sanitized and appended with the extension
        // $userName = preg_replace('/\s+/', '_', strtolower($user->name)); // Replace spaces with underscores
        // $filename = $userName . '.' . $image->getClientOriginalExtension();

        // // Store the image in the 'uploads/profile_images' directory
        // $imagePath = $image->storeAs('uploads/profile_images', $filename, 'public');

        // // Update the user's image path in the database
        // $user->image = $imagePath;
        // }

        $user->save();

        return ['data' => ProfilesResource::make($user), 'message' => 'Updated Successfully.', 'statusCode' => ApiCode::SUCCESS];
    }
}
