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
        // Find the user by email
        /** @var User $user */
        $user = $this->user->userEmail($request->email)->first();
        $version = $request->input('version', 'v1');

        // Check if user exists and password is correct
        if ($user && Hash::check($request->password, $user->password)) {
            // Register logins
            $user->registerLogin();
            // Create a token with Sanctum
            $token = $user->createToken('api-token')->plainTextToken;
            // Update or create session record
            Session::updateOrCreate(
                ['session_id' => $user->id],
                [
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'last_activity' => now(),
                    'user_data' => $version === 'v1' ? $token : "{$user->username}::{$user->email}::{$user->roles->first()->name}"
                ]
            );

            $user->load(['metas', 'branch', 'session']);

            return response()->json([
                'status' => true,
                'data' => new UserResource($user),
                'token' => $token,
                'version' => $version,
            ], 200);
        } else {
            // Authentication failed
            $errorMessage = $user ? 'La contraseña es incorrecta' : 'Las credenciales del usuario no son correctas';

            return response()->json([
                'status' => false,
                'message' => $errorMessage,
            ], 401);
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
            // Revoke all tokens...
            $user->tokens()->delete();

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
            $user->load(["session", "branch", "metas"])
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
}
