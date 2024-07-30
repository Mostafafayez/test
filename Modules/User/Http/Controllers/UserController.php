<?php

namespace Modules\User\Http\Controllers;
use App\Http\Requests\UserRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;
use Modules\User\Models\users;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
class UserController extends Controller
{
    public function signup(UserRequest $request)
    {

        $user = Users::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'password' => Hash::make($request->password),
            'type' => 'not verified',
        ]);

        return response()->json(['message' => 'User registered successfully!', 'user' => $user], 201);
    }





    public function login(UserRequest $request)
    {

        $field = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';
        
        $user = Users::where($field, $request->email_or_phone)->first();


        if ($user && Hash::check($request->password, $user->password)) {
            if ($user->type === 'verified') {

                $token = $user->createToken('Personal Access Token')->plainTextToken;

                return response()->json([
                    'message' => 'Login successful!',
                    'user' => $user,
                    'token' => $token
                ], 200);
            } else {
                return response()->json(['message' => 'User is not verified.'], 403);
            }
        }

        throw ValidationException::withMessages([
            'email_or_phone' => ['The provided credentials are incorrect.'],
        ]);

    }


    public function show (request $request){
$user=Auth::user();

return response()->json(['user' => $user], 200);




    }



    public function sendVerificationCode(Request $request)
    {
        // Validate the request
        $request->validate([
            'email_or_phone' => 'required|string',
        ]);

        $field = filter_var($request->email_or_phone, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $user = Users::where($field, $request->email_or_phone)->first();

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        // Generate a unique verification code
        $verificationCode = Str::random(6);

        $user->verification_code = Hash::make($verificationCode);
        $user->verification_code_expires_at = now()->addMinutes(15);
        $user->save();

        // Send the verification code to the user via email or SMS
        // Example for email:
        // Mail::to($user->email)->send(new \App\Mail\VerificationCode($verificationCode));

        return response()->json(['message' => 'Verification code sent'], 200);
    }








    // public function forgotPassword(Request $request)
    // {
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email|exists:user,email',
    //     ]);

    //     if ($validator->fails()) {
    //         Log::warning('Password reset request failed validation.', ['errors' => $validator->errors()]);
    //         return response()->json(['errors' => $validator->errors()], 422);
    //     }

    //     $user = Users::where('email', $request->email)->first();
    //     $token = Str::random(60); // Generate a random token

    //     // Store the token in the database
    //     DB::table('password_resets')->updateOrInsert(
    //         ['email' => $request->email],
    //         ['token' => Hash::make($token), 'created_at' => now()]
    //     );

    //     // Send the token to the user's email
    //     try {
    //         Mail::to($request->email)->send(new \App\Mail\PasswordResetMail($token));
    //         Log::info('Password reset email sent.', ['email' => $request->email]);
    //     } catch (\Exception $e) {
    //         Log::error('Failed to send password reset email.', ['email' => $request->email, 'error' => $e->getMessage()]);
    //         return response()->json(['message' => 'Failed to send password reset email.'], 500);
    //     }

    //     return response()->json(['message' => 'Password reset link sent to your email.'], 200);
    // }

    // Reset password
    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users,email',
            'token' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        if ($validator->fails()) {
            Log::warning('Password reset request failed validation.', ['errors' => $validator->errors()]);
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Validate the token
        $resetRecord = DB::table('password_resets')->where('email', $request->email)->first();

        if (!$resetRecord || !Hash::check($request->token, $resetRecord->token)) {
            Log::warning('Invalid or expired password reset token.', ['email' => $request->email]);
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        // Update the user's password
        try {
            $user = Users::where('email', $request->email)->first();
            $user->password = Hash::make($request->password);
            $user->save();

            // Delete the password reset record
            DB::table('password_resets')->where('email', $request->email)->delete();

            Log::info('Password reset successfully.', ['email' => $request->email]);
        } catch (\Exception $e) {
            Log::error('Failed to reset password.', ['email' => $request->email, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Failed to reset password.'], 500);
        }

        return response()->json(['message' => 'Password reset successfully.'], 200);
    }










public function logout(Request $request)
{
    // Ensure the user is authenticated
    if (!Auth::check()) {
        return response()->json(['message' => 'Unauthorized'], 401);
    }

    // Get the authenticated user
    $user = Auth::user();

    // Revoke all tokens for the user
    $user->tokens()->delete();

    return response()->json(['message' => 'Logged out successfully'], 200);
}






public function update(Request $request)
{

    $user = Auth::user();

    $validator = Validator::make($request->all(), [
        'name' => 'sometimes|string|max:255',
        'email' => 'sometimes|email|unique:user,email,' . $user->id,
        'phone' => 'sometimes|string|unique:user,phone,' . $user->id,
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }


    $user->update($request->only('name', 'email', 'phone'));

    return response()->json(['message' => 'User updated successfully', 'user' => $user], 200);
}



public function loginWithGoogle(Request $request)
{
    $accessToken = $request->input('access_token');

    if (!$accessToken) {
        return response()->json(['error' => 'Access token is required'], 400);
    }

    try {
        $user = Socialite::driver('google')->stateless()->userFromToken($accessToken);
    }
        catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred: ' . $e->getMessage()], 500);
        }
        if (!$user) {
            return response()->json(['error' => 'Failed to authenticate with Google'], 400);
        }

        $existingUser = Users::where('email', $user->getEmail())->first();

        if (!$existingUser) {
            $existingUser = Users::create([
                'name' => $user->getName(),
                'email' => $user->getEmail(),
                // 'password' => bcrypt(str_random(25)),
            ]);
        }

        $token = $existingUser->createToken('auth_token')->plainTextToken;

        return response()->json(['token' => $token]);
}
}
