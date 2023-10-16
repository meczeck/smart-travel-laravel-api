<?php

namespace App\Http\Controllers\API\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserProfile\UpdateUserProfileRequest;
use App\Http\Resources\Auth\GetUserResource;
use App\Models;
use App\Models\User;
use Auth;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    public function show($id)
    {
        $user = User::find($id);
        $response = ['user' => $user, 'permissions' => $user->getAllPermissions()];

        return new GetUserResource($response);
    }

    public function updateProfile(UpdateUserProfileRequest $request, $id)
    {
        try {
            $user = User::find($id);

            if ($user) {
                $user->name = $request->input('name');
                $user->phone = $request->input('phone');
                $user->email = $request->input('email');

                return response()->json(['message' => 'success'], 200);
            }
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
                'message' => 'someting went wron in UserProfileController.update'
            ], 500);
        }
    }

    public function changePassword(Request $request, $id)
    {
        $request->validate([
            'old_password' => 'required',
            'new_password' => 'required|confirmed'
        ]);

        $user = User::findOrFail($id);

        if (Hash::check($request->input('old_password'), $user->password)) {
            $user->password = $request->input('new_password');
            $user->save();

            return response()->json(['message' => 'Password changed successfully'], 200);
        }

        return response()->json(['message' => "Password do not found in our records"], 404);
    }

}