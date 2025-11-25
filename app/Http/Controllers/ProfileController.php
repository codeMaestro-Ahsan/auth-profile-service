<?php

namespace App\Http\Controllers;
use App\Http\Resources\ProfileResource;
use App\Http\Requests\Profile\UpdateProfileRequest;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show(Request $request){
        return new ProfileResource($request->user()->profile);
    }
    public function update(UpdateProfileRequest $request){
        $profile = $request->user()->profile;
        $data = $request->validated();
        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $data['avatar'] = $path;
        }
        $profile->update($data);
        return response()->json([
            'message'=>'Profile updated successfully',
            'data'=>new ProfileResource($profile),
        ]);
    }
}
