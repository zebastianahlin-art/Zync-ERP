<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Core\Controller;
use App\Core\JwtService;
use App\Core\TotpService;
use App\Models\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * API authentication controller.
 *
 * Handles JWT-based login, 2FA verification, token refresh and current-user info.
 * All responses are JSON; no HTML is returned.
 */
class AuthApiController extends Controller
{
    private JwtService     $jwt;
    private TotpService    $totp;
    private UserRepository $repo;

    public function __construct()
    {
        parent::__construct();
        $this->jwt  = new JwtService();
        $this->totp = new TotpService();
        $this->repo = new UserRepository();
    }

    /**
     * POST /api/v1/login
     *
     * Body: {"email": "...", "password": "..."}
     *
     * Success (no 2FA):  {"token": "...", "expires_in": 3600, "user": {...}}
     * Success (2FA on):  {"requires_2fa": true, "temp_token": "..."}
     * Failure:           401 {"error": "Unauthorized", "message": "..."}
     */
    public function login(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body     = (array) $request->getParsedBody();
        $email    = trim((string) ($body['email'] ?? ''));
        $password = (string) ($body['password'] ?? '');

        if ($email === '' || $password === '') {
            return $this->json($response, [
                'error'   => 'Unauthorized',
                'message' => 'E-postadress och lösenord krävs',
            ], 401);
        }

        $user = $this->repo->findByEmail($email);

        if ($user === null || !password_verify($password, $user->passwordHash)) {
            return $this->json($response, [
                'error'   => 'Unauthorized',
                'message' => 'Felaktig e-postadress eller lösenord',
            ], 401);
        }

        // If 2FA is enabled, issue a short-lived temp token
        if ($user->totpEnabled === 1) {
            $tempToken = $this->jwt->generateToken([
                'user_id'  => $user->id,
                'email'    => $user->email,
                'tfa_step' => true,
            ], 300); // 5 minutes

            return $this->json($response, [
                'requires_2fa' => true,
                'temp_token'   => $tempToken,
            ]);
        }

        $expiresIn = 3600;
        $token     = $this->jwt->generateToken([
            'user_id' => $user->id,
            'email'   => $user->email,
        ], $expiresIn);

        return $this->json($response, [
            'token'      => $token,
            'expires_in' => $expiresIn,
            'user'       => [
                'id'    => $user->id,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * POST /api/v1/2fa/verify
     *
     * Body: {"temp_token": "...", "code": "123456"}
     *
     * Success: {"token": "...", "expires_in": 3600, "user": {...}}
     * Failure: 401 {"error": "Unauthorized", "message": "..."}
     */
    public function verify2fa(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $body      = (array) $request->getParsedBody();
        $tempToken = (string) ($body['temp_token'] ?? '');
        $code      = trim((string) ($body['code'] ?? ''));

        $payload = $this->jwt->validateToken($tempToken);

        if ($payload === null || empty($payload['tfa_step'])) {
            return $this->json($response, [
                'error'   => 'Unauthorized',
                'message' => 'Ogiltigt eller utgånget temp-token',
            ], 401);
        }

        $userId = (int) ($payload['user_id'] ?? 0);
        $user   = $this->repo->findById($userId);

        if ($user === null || $user->totpSecret === null) {
            return $this->json($response, [
                'error'   => 'Unauthorized',
                'message' => 'Ogiltigt token',
            ], 401);
        }

        if (!$this->totp->verifyCode($user->totpSecret, $code)) {
            return $this->json($response, [
                'error'   => 'Unauthorized',
                'message' => 'Felaktig verifieringskod',
            ], 401);
        }

        $expiresIn = 3600;
        $token     = $this->jwt->generateToken([
            'user_id' => $user->id,
            'email'   => $user->email,
        ], $expiresIn);

        return $this->json($response, [
            'token'      => $token,
            'expires_in' => $expiresIn,
            'user'       => [
                'id'    => $user->id,
                'email' => $user->email,
            ],
        ]);
    }

    /**
     * POST /api/v1/token/refresh
     *
     * Header: Authorization: Bearer <current_token>
     *
     * Success: {"token": "...", "expires_in": 3600}
     * Failure: 401 {"error": "Unauthorized", "message": "..."}
     */
    public function refresh(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $authHeader = $request->getHeaderLine('Authorization');
        $token      = str_starts_with($authHeader, 'Bearer ') ? substr($authHeader, 7) : '';

        $expiresIn = 3600;
        $newToken  = $this->jwt->refreshToken($token, $expiresIn);

        if ($newToken === null) {
            return $this->json($response, [
                'error'   => 'Unauthorized',
                'message' => 'Ogiltigt eller utgånget token',
            ], 401);
        }

        return $this->json($response, [
            'token'      => $newToken,
            'expires_in' => $expiresIn,
        ]);
    }

    /**
     * GET /api/v1/me
     *
     * Header: Authorization: Bearer <token>
     *
     * Success: {"id": 1, "email": "..."}
     */
    public function me(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        /** @var array<string, mixed> $jwtUser */
        $jwtUser = $request->getAttribute('jwt_user');

        return $this->json($response, [
            'id'    => $jwtUser['user_id'] ?? null,
            'email' => $jwtUser['email'] ?? null,
        ]);
    }
}
