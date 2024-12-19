<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function view(User $user)
    {
        return new UserResource($user);

    }



    public function index(Request $request, $user)
    {

        $itensPerPage = $request->input('itensPerPage', 10);


            return UserResource::collection($user->usersCreated()->orderBy('created_at', 'desc')->paginate($itensPerPage));
        }


    public function update(UpdateUserRequest $request, User $user)
    {
        $user->fill($request->validated());
        $user->save();

        return new UserResource($user);
    }

public function store(StoreUserRequest $request)
{

}




    public function showMe(Request $request): UserResource
    {
        return new UserResource($request->user());
    }

    public function storeMe(StoreUserRequest $request): UserResource
    {
        // Create the user
        $user = new User();
        $user->fill($request->only(['name', 'email', 'nickname']));

        // Hash the password before storing it
        $user->password = bcrypt($request->input('password'));
        $user->brain_coins_balance = 10;
        $user->type = 'P';

        // Generate a remember_token
        $user->remember_token = Str::random(10);

        // Handle photo if provided
        if ($request->has('photo')) {
            $imageName = $this->handlePhoto($request->input('photo'));
            if ($imageName) {
                $user->photo_filename = $imageName;
            }
        }

        // Save the user record to the database
        $user->save();

        // Log the initial transaction (assuming a bonus is being added)
        $user->transactions()->create([
            'type' => 'B', // 'B' represents a bonus or initial transaction type
            'transaction_datetime' => now(),
            'brain_coins' => 10,
        ]);

        // Fire the Registered event (for mail, notifications, etc.)
        event(new Registered($user));

        // Return the user resource
        return new UserResource($user);
    }

    public function updateMe(UpdateUserRequest $request): UserResource
    {
        $validatedData = $request->validated();

        $user = $request->user();

        // Update basic user details
        $user->name = $validatedData['name'];
        $user->email = $validatedData['email'];
        $user->nickname = $validatedData['nickname'] ?? $user->nick_name;

        // Handle the photo if provided (Base64 encoding)
        if ($request->has('photo')) {
            $imageName = $this->handlePhoto($request->input('photo'));
            if ($imageName) {
                $user->photo_filename = $imageName;
            }
        }

        // Save the updated user
        $user->save();

        return new UserResource($user);
    }

    public function deleteMe(Request $request)
    {
        $user = $request->user();
        $user->brain_coins_balance = 0;
        $user->save();
        $user->delete();
    }

    // Private method to handle photo upload
    private function handlePhoto($base64Image)
    {
        // Match the image's MIME type and data (e.g., 'data:image/jpeg;base64,')
        if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
            // Remove the "data:image/*;base64," part
            $imageData = substr($base64Image, strpos($base64Image, ',') + 1);

            // Decode the base64 data
            $imageData = base64_decode($imageData);

            // Generate a unique file name
            $imageName = Str::random(40) . '.' . $matches[1];

            // Store the image on disk (public storage)
            $imagePath = 'photos/' . $imageName;
            Storage::disk('public')->put($imagePath, $imageData);

            return $imageName;
        }

        return null;
    }




}
