<?php

namespace Modules\Admin\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Modules\Admin\Http\Requests\adminrequest;
use Modules\Admin\Models\admins;
use Modules\User\Models\User;
use Illuminate\Validation\ValidationException;
use Modules\User\Models\users;
use Illuminate\Database\Eloquent\ModelNotFoundException;
class AdminController extends Controller
{
    // Sign up method for users
    public function signup(adminrequest $request)
    {

        $user = admins::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'type' => 'not verified',
        ]);

        return response()->json(['message' => 'admin registered successfully!', 'admin' => $user], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }


        $admin = admins::where('email', $request->email)->first();

        if ($admin && Hash::check($request->password, $admin->password)) {

            return response()->json(['message' => 'Login successful!', 'admin' => $admin], 200);
        }

        return response()->json(['message' => 'Invalid credentials.'], 401);
    }



public function verifyUser(Request $request)
{
    $validator = Validator::make($request->all(), [
        'user_id' => 'required|exists:user,id',
        'verification_code' => 'required|string',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = Users::find($request->user_id);

    if ($user->verification_code_expires_at < now()) {
        return response()->json(['message' => 'Verification code expired'], 400);
    }

    if (!Hash::check($request->verification_code, $user->verification_code)) {
        return response()->json(['message' => 'Invalid verification code'], 400);
    }

    // Verify the user
    if ($user->type === 'verified') {
        return response()->json(['message' => 'User is already verified.'], 200);
    }

    $user->type = 'verified';
    $user->verification_code = null; // Clear the code
    $user->verification_code_expires_at = null; // Clear the expiration time
    $user->save();

    return response()->json(['message' => 'User verified successfully!', 'user' => $user], 200);
}




    public function deleteUser($id)
    {
        try {
            // Find the user by ID
            $user = Users::findOrFail($id);

            // Delete the user
            $user->delete();

            return response()->json(['message' => 'User deleted successfully!'], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json(['message' => 'User not found.'], 404);
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred.'], 500);
        }
    }
}
