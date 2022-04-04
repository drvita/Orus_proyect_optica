<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\User;
use App\Models\Session;
use App\Http\Resources\UserNoty;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\AuthRequest;
use Carbon\Carbon;


class AuthController extends Controller
{
    public function __construct(User $user)
    {
        // $this->middleware('can:auth.access')->only('login');
        $this->user = $user;
    }
    public function login(AuthRequest $request)
    {
        $user = $this->user
            ->userEmail($request->email)
            ->publish()
            ->withRelation()
            ->first();

        if ($user && Hash::check($request->password, $user->password)) {
            $user->api_token = hash('sha256', Str::random(60));
            $user->save();
            $session = Session::where("session_id", $user->id)->first();
            if ($session) {
                $session->ip_address = getenv("REMOTE_ADDR");
                $session->user_agent = getenv("HTTP_USER_AGENT");
                $session->last_activity = new Carbon();
                $session->user_data = $user->api_token;
                $session->save();
            } else {
                $session = Session::create([
                    'session_id' => $user->id,
                    'ip_address' => getenv("REMOTE_ADDR"),
                    'user_agent' => getenv("HTTP_USER_AGENT"),
                    'last_activity' => new Carbon(),
                    'user_data' => $user->api_token
                ]);
            }

            $respons["status"] = true;
            $respons['data'] = new UserResource($user);
            $respons['message'] = "Bienvenido al sistema";
            $respons['token'] = $user->api_token;
            return response()->json($respons, 200);
        } else {
            $respons["status"] = false;
            $respons['message'] = $user ? "La contrasena es incorrecta" : "Las credenciales del usuario no son correctas";
            $respons['errnum'] = $user ? $user->id : 0;
            return response()->json($respons, 401);
        }
    }
    public function logout(Request $request)
    {
        $user = $request->user();

        if ($user) {
            $user->api_token = null;
            $user->save();
            return response()->json(['message' => 'Ha salido del sistema correctamente'], 200);
        }

        return response()->json(['message' => 'Usted no tiene una session activa'], 401);
    }
    public function userData(Request $request)
    {
        return new UserNoty($request->user()->load("session")->load("branch"));
    }
    public function userReadNotify(Request $request)
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
    public function userSubscriptionNotify(Request $request)
    {
        return response()->json(["success" => true], 200);
    }
}
