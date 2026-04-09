<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        Log::info('auth.login.attempt', [
            'email' => $request->input('email'),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
        ]);

        if (! $token = auth('api')->attempt($credentials)) {
            Log::warning('auth.login.failed_invalid_credentials', [
                'email' => $request->input('email'),
                'ip' => $request->ip(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = auth('api')->user();

        if (! $user->is_active) {
            Log::warning('auth.login.failed_inactive_user', [
                'user_id' => $user->id,
                'email' => $user->email,
                'ip' => $request->ip(),
            ]);

            auth('api')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive. Contact administrator.',
            ], 403);
        }

        Log::info('auth.login.success', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role_names' => $user->getRoleNames()->values()->all(),
            'ip' => $request->ip(),
            'token_segments' => substr_count($token, '.') + 1,
            'expires_in_seconds' => auth('api')->factory()->getTTL() * 60,
        ]);

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        try {
            $user = auth('api')->user();
            auth('api')->logout(true);

            Log::info('auth.logout.success', [
                'user_id' => $user?->id,
                'email' => $user?->email,
            ]);
        } catch (JWTException $e) {
            Log::warning('auth.logout.failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token could not be invalidated.',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ]);
    }

    public function refresh(): JsonResponse
    {
        try {
            $newToken = auth('api')->refresh();

            Log::info('auth.refresh.success', [
                'user_id' => auth('api')->id(),
                'token_segments' => substr_count($newToken, '.') + 1,
                'expires_in_seconds' => auth('api')->factory()->getTTL() * 60,
            ]);

            return $this->respondWithToken($newToken);
        } catch (TokenExpiredException $e) {
            Log::warning('auth.refresh.failed_expired', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token has expired and cannot be refreshed.',
            ], 401);
        } catch (TokenInvalidException $e) {
            Log::warning('auth.refresh.failed_invalid', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
            ], 401);
        } catch (JWTException $e) {
            Log::warning('auth.refresh.failed_jwt', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token could not be refreshed.',
            ], 401);
        }
    }

    public function me(): JsonResponse
    {
        try {
            $user = auth('api')->user();
        } catch (TokenExpiredException $e) {
            Log::warning('auth.me.failed_expired', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token has expired.',
            ], 401);
        } catch (TokenInvalidException $e) {
            Log::warning('auth.me.failed_invalid', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token is invalid.',
            ], 401);
        } catch (JWTException $e) {
            Log::warning('auth.me.failed_jwt', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Token could not be parsed.',
            ], 401);
        }

        if (! $user) {
            Log::warning('auth.me.failed_unauthenticated');

            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated.',
            ], 401);
        }

        Log::info('auth.me.success', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Authenticated user retrieved successfully.',
            'data'    => new UserResource($user),
        ]);
    }

    private function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Authentication successful.',
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'Bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
                'user'         => new UserResource(auth('api')->user()),
            ],
        ]);
    }
}
