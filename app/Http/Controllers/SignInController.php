<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\AccessTokenModel;
use App\Models\UserProviderModel;
use Illuminate\Support\Str;

class SignInController extends Controller
{
    public function index(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        // Validate required fields
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }

        // Find user by email
        $user = User::where('email', $email)->first();

        if (!$user) {
            return $this->responseError('gagal login');
        }

        // Verify password
        if (!password_verify($password, $user->password)) {
            return $this->responseError('gagal login');
        }

        $token = $user->createToken('auth-token', ['*'], now()->addMinutes(config('auth.login_time')))->plainTextToken;

        return $this->responseSuccess("success", [
            'token' => $token,
            'user' => [
                'id_user' => $user->id_user,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    /**
     * Validate user token
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function validateToken(Request $request)
    {
        try {
            // Get token from request header
            $token = $request->bearerToken();

            if (!$token) {
                return $this->responseError('Token not provided');
            }

            // Get authenticated user
            $user = auth()->user();

            // User should never be null here because of auth:sanctum middleware
            // but we'll keep the check as a safeguard
            if (!$user) {
                return $this->responseError('Invalid token');
            }

            // Update token expiration time
            DB::table('personal_access_tokens')
                ->where('token', hash('sha256', $token))
                ->update([
                    'expires_at' => now()->addMinutes(config('auth.login_time'))
                ]);

            return $this->responseSuccess('Token is valid', [
                'user' => [
                    'id_user' => $user->id_user,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            return $this->responseError('Error validating token: ' . $e->getMessage());
        }
    }

    /**
     * Get all active tokens for the authenticated user
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getActiveTokens()
    {
        try {
            // Get authenticated user
            $user = auth()->user();

            if (!$user) {
                return $this->responseError('User not authenticated');
            }

            // Get all active tokens for the user
            $tokens = $user->tokens->map(function ($token) {
                // Get token expiration from the expires_at column
                $expiresAt = $token->expires_at;

                // Calculate remaining lifetime
                $lifetime = $expiresAt ? intdiv($expiresAt->timestamp - now()->timestamp, 60) : 0;

                return [
                    'id' => $token->id,
                    'name' => $token->name,
                    'created_at' => $token->created_at->format('Y-m-d H:i:s'),
                    'last_used_at' => $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i:s') : null,
                    'expires_at' => $expiresAt ? $expiresAt->format('Y-m-d H:i:s') : null,
                    'expires_in' => $lifetime > 0 ? $lifetime . ' minutes' : 'expired'
                ];
            });

            return $this->responseSuccess('Active tokens retrieved successfully', [
                'tokens' => $tokens
            ]);
        } catch (\Exception $e) {
            return $this->responseError('Error retrieving tokens: ' . $e->getMessage());
        }
    }

    public function indexProvider(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        $idProvider = $request['id_provider'];

        // Validate required fields
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required'
        ]);

        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }

        // Find user by email
        $user = UserProviderModel::where('id_provider', $idProvider)
            ->where('email', $email)
            ->first();

        if (!$user) {
            return $this->responseError('gagal login');
        }

        // Verify password
        if (!password_verify($password, $user->password)) {
            return $this->responseError('gagal login');
        }

        $token = AccessTokenModel::create([
            'id_user' => $user->id,
            'token' => Str::random(64),
            'hit' => 0,
            'expires_at' => now()->addMinutes(config('auth.login_time')),
        ]);

        return $this->responseSuccess("success", [
            'token' => $token['token'],
            'user' => [
                'id_user' => $user->id_user,
                'name' => $user->name,
                'email' => $user->email,
            ]
        ]);
    }

    public function validateTokenProvider(Request $request)
    {
        try {
            // Get token from request header
            $token = $request->bearerToken();
            $idProvider = $request['id_provider'];
            if (!$token) {
                return $this->responseError('Token not provided');
            }

            // Get authenticated user
            $validateToken = AccessTokenModel::where('token', $token)->first();
            if (!$validateToken) {
                return $this->responseError('Invalid token');
            }

            // Check if token has expired
            if ($validateToken->expires_at < now()) {
                return $this->responseError('Invalid token');
            }

            // Check is user available
            $user = $validateToken->user()->first();
            if (!$user) {
                return $this->responseError('Invalid token');
            }

            // Update token expiration time
            $validateToken->update([
                'last_used_at' => now(),
                'hit' => $validateToken->hit + 1,
                'expires_at' => now()->addMinutes(config('auth.login_time'))
            ]);

            return $this->responseSuccess('Token is valid', [
                'user' => [
                    'id_user' => $user->id_user,
                    'name' => $user->name,
                    'email' => $user->email
                ]
            ]);
        } catch (\Exception $e) {
            return $this->responseError('Error validating token: ' . $e->getMessage());
        }
    }
}
