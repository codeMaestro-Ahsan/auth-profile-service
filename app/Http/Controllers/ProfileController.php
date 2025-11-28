<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProfileResource;
use App\Http\Requests\Profile\UpdateProfileRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show(Request $request)
    {
        $profile = $request->user()->profile;

        // Policy check
        $this->authorize('view', $profile);

        return new ProfileResource($profile);
    }

    public function update(UpdateProfileRequest $request)
    {
        // Policy already runs in UpdateProfileRequest → authorize()

        $profile = $request->user()->profile;
        $data = $request->validated();

        DB::beginTransaction();

        try {
            // Handle Avatar Upload
            if ($request->hasFile('avatar')) {

                // Delete old avatar if exists
                if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                    Storage::disk('public')->delete($profile->avatar);
                }

                // Store new avatar
                $path = $request->file('avatar')->store('avatars', 'public');
                $data['avatar'] = $path;
            }

            // Update profile fields
            $profile->update($data);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile updated successfully',
                'data' => new ProfileResource($profile),
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to update profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function destroy(Request $request)
    {
        $profile = $request->user()->profile;

        // Policy check
        $this->authorize('delete', $profile);

        DB::beginTransaction();

        try {
            // Delete avatar if exists
            if ($profile->avatar && Storage::disk('public')->exists($profile->avatar)) {
                Storage::disk('public')->delete($profile->avatar);
            }

            // Delete profile
            $profile->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Profile deleted successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Failed to delete profile',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
