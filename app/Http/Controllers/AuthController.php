<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Session;
use App\Http\Resources\UserNoty;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\AuthRequest;
use Illuminate\Http\JsonResponse;
use App\Services\PasswordResetService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    /**
     * User model instance.
     */
    public function __construct(private User $user) {}

    /**
     * Authenticate the user and create a new API token using Sanctum
     *
     * @param AuthRequest $request
     * @return JsonResponse
     */
    public function login(AuthRequest $request): JsonResponse
    {
        Log::debug('Login attempt started', ['email' => $request->email]);
        try {
            // Find the user by email
            /** @var User $user */
            $user = $this->user->userEmail($request->email)->first();
            $version = $request->input('version', 'v1');

            Log::debug('User lookup completed', [
                'user_found' => (bool)$user,
                'version' => $version
            ]);

            // Check if user exists and password is correct
            if ($user && Hash::check($request->password, $user->password)) {
                Log::debug('Password check successful');

                // Register logins
                $user->registerLogin();
                Log::debug('Login registered');

                // Create a token with Sanctum
                $token = $user->createToken('api-token')->plainTextToken;
                Log::debug('Token created');

                // Update or create session record
                Log::debug('Updating session', [
                    'user_id' => $user->id,
                    'ip' => $request->ip()
                ]);

                // Possible crash point: $user->roles->first()->name
                $userData = $version === 'v1' ? $token : "{$user->username}::{$user->email}::" . ($user->roles->first() ? $user->roles->first()->name : 'no-role');

                Session::updateOrCreate(
                    ['session_id' => $user->id],
                    [
                        'ip_address' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'last_activity' => now(),
                        'user_data' => $userData
                    ]
                );
                Log::debug('Session updated');

                $user->load(['metas', 'branch', 'session']);
                Log::debug('User relations loaded');

                return response()->json([
                    'status' => true,
                    'data' => new UserResource($user),
                    'token' => $token,
                    'version' => $version,
                ], 200);
            } else {
                Log::debug('Authentication failed: user not found or password incorrect');
                // Authentication failed
                $errorMessage = $user ? 'La contraseña es incorrecta' : 'Las credenciales del usuario no son correctas';

                return response()->json([
                    'status' => false,
                    'message' => $errorMessage,
                ], 401);
            }
        } catch (\Exception $e) {
            Log::error('Login Server Error', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'status' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage(),
            ], 500);
        }
    }
    /**
     * Log the user out (revoke the token)
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        $user = $request->user();

        if ($user) {
            $user->tokens()->delete();
            $user->registerLogout();
            return response()->json(['message' => 'Logged out successfully'], 200);
        }

        return response()->json(['message' => 'Usted no tiene una session activa'], 401);
    }
    /**
     * Get authenticated user data
     *
     * @param Request $request
     * @return UserNoty
     */
    public function userData(Request $request): UserNoty
    {
        $user = $request->user();

        return new UserNoty(
            $user->load(["session", "branch", "metas", "phones"])
        );
    }
    /**
     * Mark user notifications as read
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userReadNotify(Request $request): JsonResponse
    {
        $res = ["success" => false, "id" => $request->id];
        $code = 200;
        foreach ($request->user()->unreadNotifications as $notification) {
            if ($request->id === -1) {
                $notification->markAsRead();
                $res = ["success" => true, "id" => $request->id];
                $code = 200;
            } else if ($request->id === $notification->id) {
                $notification->markAsRead();
                $res = ["success" => true, "id" => $request->id];
                $code = 200;
                break;
            }
        }
        return response()->json($res, $code);
    }
    /**
     * Handle user notification subscription
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function userSubscriptionNotify(Request $request): JsonResponse
    {
        return response()->json(["success" => true], 200);
    }

    /**
     * Request a password reset token.
     *
     * @param Request $request
     * @param PasswordResetService $service
     * @return JsonResponse
     */
    public function requestPasswordReset(Request $request, PasswordResetService $service): JsonResponse
    {
        $user = $request->user();
        $targetUser = $user;

        // Check if admin and requesting for another user
        if ($user->hasRole('admin') && $request->has('user_id')) {
            $targetUser = User::find($request->user_id);
            if (!$targetUser) {
                return response()->json(['message' => 'Usuario no encontrado'], 404);
            }
        }

        $token = $service->generateToken($targetUser);
        Log::info('Password reset token generated for user: ' . $targetUser->email);
        return response()->json([
            'status' => true,
            'message' => 'Token generado exitosamente',
            'token' => $token
        ]);
    }

    /**
     * Publicly request a password reset token via email.
     *
     * @param Request $request
     * @param PasswordResetService $service
     * @return JsonResponse
     */
    public function publicRequestPasswordReset(Request $request, PasswordResetService $service): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $service->generateToken($user, true);
        }

        // Always return the same message for security
        Log::info('Password reset token generated for user: ' . $request->email);
        return response()->json([
            'status' => true,
            'message' => 'Si el correo existe en nuestro sistema, se ha generado un token. Por favor comuníquese con su administrador para obtenerlo.'
        ]);
    }

    /**
     * Validate a password reset token.
     *
     * @param Request $request
     * @param PasswordResetService $service
     * @return JsonResponse
     */
    public function validateResetToken(Request $request, PasswordResetService $service): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:16',
            'email' => 'required|email|exists:users,email',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $targetUser = $service->validateToken($request->token, $request->email);

        if (!$targetUser) {
            return response()->json(['status' => false, 'message' => 'Token o Email inválido'], 404);
        }

        return response()->json([
            'status' => true,
            'message' => 'Token válido',
        ]);
    }

    /**
     * Reset the user's password using a token.
     *
     * @param Request $request
     * @param PasswordResetService $service
     * @return JsonResponse
     */
    public function resetPassword(Request $request, PasswordResetService $service): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'token' => 'required|string|size:16',
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()], 422);
        }

        $targetUser = $service->validateToken($request->token, $request->email);

        if (!$targetUser) {
            return response()->json(['status' => false, 'message' => 'Token o Email inválido'], 404);
        }

        $targetUser->password = Hash::make($request->password);
        $targetUser->remember_token = null; // Clear token after use
        $targetUser->save();
        Log::info('Password reset successfully for user: ' . $targetUser->email);
        return response()->json([
            'status' => true,
            'message' => 'Contraseña actualizada exitosamente'
        ]);
    }
}
