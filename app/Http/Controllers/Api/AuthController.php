<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        if (! $token = auth('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = auth('api')->user();

        if (! $user->is_active) {
            auth('api')->logout();
            return response()->json([
                'success' => false,
                'message' => 'Account is inactive. Contact administrator.',
            ], 403);
        }

        return $this->respondWithToken($token);
    }

    public function logout(): JsonResponse
    {
        auth('api')->logout(true);

        return response()->json([
            'success' => true,
            'message' => 'Successfully logged out.',
        ]);
    }

    public function refresh(): JsonResponse
    {
        try {
            $newToken = auth('api')->refresh();
            return $this->respondWithToken($newToken);
        } catch (JWTException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Token could not be refreshed.',
            ], 401);
        }
    }

    public function me(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => new UserResource(auth('api')->user()),
        ]);
    }

    private function respondWithToken(string $token): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data'    => [
                'access_token' => $token,
                'token_type'   => 'bearer',
                'expires_in'   => auth('api')->factory()->getTTL() * 60,
                'user'         => new UserResource(auth('api')->user()),
            ],
        ]);
    }
}
