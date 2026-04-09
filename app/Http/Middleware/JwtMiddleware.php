<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;

class JwtMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $authorizationHeader = trim((string) $request->header('Authorization', ''));

        if ($authorizationHeader === '') {
            return $this->unauthorized('Token not provided.');
        }

        if (! preg_match('/^Bearer\s+(.+)$/i', $authorizationHeader, $matches)) {
            return $this->unauthorized('Invalid authorization header format. Use: Bearer <token>.');
        }

        $token = trim($matches[1]);

        // Normalize duplicate prefixes like: "Bearer Bearer <token>"
        while (Str::startsWith(Str::lower($token), 'bearer ')) {
            $token = trim(substr($token, 7));
        }

        if ($token === '' || in_array(Str::lower($token), ['null', 'undefined'], true)) {
            return $this->unauthorized('Token is empty or invalid.');
        }

        // Quick structural validation for JWT: header.payload.signature
        if (substr_count($token, '.') !== 2) {
            return $this->unauthorized('Malformed JWT token. Expected 3 segments.');
        }

        // Set normalized header so JWTAuth always parses a clean token.
        $request->headers->set('Authorization', 'Bearer '.$token);

        try {
            JWTAuth::parseToken()->authenticate();
        } catch (TokenExpiredException $e) {
            return $this->unauthorized('Token has expired.');
        } catch (TokenInvalidException $e) {
            return $this->unauthorized('Token is invalid.');
        } catch (JWTException $e) {
            return $this->unauthorized('Token could not be parsed.');
        }

        return $next($request);
    }

    private function unauthorized(string $message): Response
    {
        return response()->json([
            'success' => false,
            'message' => $message,
        ], 401);
    }
}
