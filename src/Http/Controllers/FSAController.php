<?php

namespace Tarek\Fsa\Http\Controllers;

use App\Models\User;
use Tarek\Fsa\Traits\HttpResponses;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class FSAController extends Controller
{
    use HttpResponses;

    public function login(Request $request) :JsonResponse
    {
        $credentials = $request->validate([
            "email" => "required|string|email|max:255",
            "password" => "required|string|min:6|max:30"
        ]);
        if ( !Auth::attempt($credentials) ) {
            return $this->error('','Email or Password not correct',401);
        }
        $user = Auth::user();
        if ( $user->isMustVerifyEmail() && !$user->hasVerifiedEmail() ) {
            $user->sendEmailVerificationNotification();
            return $this->error('','Email Not verified yet, We have sent another Verification Email',401);
        }
        return $this->success([
            "user" => $user,
            'token' => $user->createToken('API TOKEN OF ' . $user->name)->plainTextToken
        ]);
    }

    public function register(Request $request) :JsonResponse
    {
        $credentials = $request->validate([
            "name" => "required|string|max:255",
            "email" => "required|email|max:255|unique:users,email",
            "password" => ["required", "confirmed", Password::defaults()]
        ]);
        /*$request->validated($request->all());*/
        $user = User::create([
            'name' => $credentials['name'],
            'email' => $credentials['email'],
            'password' => Hash::make($credentials['password'])
        ]);
        event(new Registered($user));
        if ( $user->isMustVerifyEmail() ) {
            return $this->success([],"Email Verification Sent");
        }
        return $this->success([
            "user" => $user,
            'token' => $user->createToken('API TOKEN OF ' . $user->name)->plainTextToken
        ]);
    }

    public function mobileLogin(Request $request) :JsonResponse
    {
        $credentials = $request->validate([
            "email" => "required|string|email",
            "password" => "required|string|min:6",
            "device_name" => "required|string",
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            return $this->error('','Email or Password not correct',401);
        }

        if ( $user->isMustVerifyEmail() && !$user->hasVerifiedEmail() ) {
            $user->sendEmailVerificationNotification();
            return $this->error('','Email Not verified yet, We have sent another Verification Email',401);
        }

        return $this->success([
            "user" => $user,
            "token" => $user->createToken($request->device_name)->plainTextToken
        ]);
    }

    public function emailVerification(string $id, string $hash) :JsonResponse
    {
        $user = User::find($id);
        abort_if(!$user, 403);
        abort_if(!hash_equals(sha1($user->getEmailForVerification()), $hash), 403);
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
            return $this->success([
                "email" => "Verified Successfully"
            ]);
        }
        return $this->error([],"Email already verified", 403);
    }

    public function logout() :JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();
        return $this->success([], "You logged Out");
    }

    public function forgotPassword(Request $request) :JsonResponse
    {
        $request->validate(['email' => 'required|email']);
        $status = Password::sendResetLink($request->only('email'));
        if ( $status === Password::RESET_LINK_SENT ) return $this->success(['status' => __($status)]);
        return $this->error([], __($status),404);
    }

    public function resetPassword(Request $request) :JsonResponse
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
                $user->save();
                event(new PasswordReset($user));
            }
        );
        if ( $status === Password::PASSWORD_RESET ) return $this->success(['status' => __($status)]);
        return $this->error([], __($status),422);
    }

    /**
     * Redirect the user to the Provider authentication page.
     *
     * @return JsonResponse
     */
    public function redirectToProvider(string $provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) return $validated;
        return Socialite::driver($provider)->stateless()->redirect();
    }

    /**
     * Obtain the user information from Provider.
     *
     * @return JsonResponse
     */
    public function handleProviderCallback(string $provider)
    {
        $validated = $this->validateProvider($provider);
        if (!is_null($validated)) return $validated;
        try {
            $user = Socialite::driver($provider)->stateless()->user();
        } catch (ClientException $exception) {
            return $this->error([],"Invalid credentials provided.", 422);
        }

        $userCreated = User::firstOrCreate(
            ['email' => $user->getEmail()],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'status' => true,
            ]
        );
        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ],
            ['avatar' => $user->getAvatar()]
        );
        $token = $userCreated->createToken($provider)->plainTextToken;
        /*return $this->success(['Access-Token' => $token]);*/
        return response()->json($userCreated, 200, ['Access-Token' => $token]);
    }

    protected function validateProvider(string $provider)
    {
        if (!in_array($provider, ['facebook', 'github', 'google'])) {
            return $this->error([],"Please login using facebook, github or google",422);
        }
    }

}
