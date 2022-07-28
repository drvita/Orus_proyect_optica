<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\UserRequest;
use App\User;
use App\Models\Session;
use Carbon\Carbon;


class UserController extends Controller
{
    public function __construct(User $user)
    {
        $this->middleware('can:user.list')->only('index');
        $this->middleware('can:user.show')->only('show');
        $this->middleware('can:user.add')->only('store');
        $this->middleware('can:user.edit')->only('update');
        $this->middleware('can:user.delete')->only('destroy');
        $this->middleware('can:auth.closeSession')->only('clearToken');
        $this->user = $user;
    }
    /**
     * Muestra la lista de usuarios en sistema
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;
        $deleted = true;

        if (isset($request->deleted)) {
            $deleted = (int) $request->deleted;
            $deleted = (bool) $deleted;
        }

        $users = $this->user
            ->with('session', 'branch')
            ->orderBy($orderby, $order)
            ->search($request->search)
            ->userName($request->username, $request->userId)
            ->userEmail($request->email, $request->userId)
            ->role($request->role)
            ->branchId($request->branch_id)
            ->nobot()
            ->publish($deleted)
            ->paginate($page);

        return UserResource::collection($users);
    }
    /**
     * Registra un nuevo usuario en la base de datos.
     * @param  $request que se traen de post body json
     * @return Json api rest
     */
    public function store(UserRequest $request)
    {
        $currentUser = $request->user();
        $request['password'] = Hash::make($request->password);
        $request['branch_id'] = $currentUser->branch_id;

        $user = User::create([
            'name' => strtolower($request->name),
            'username' => strtolower($request->username),
            'email' => strtolower($request->email),
            'password' => $request->password,
            'branch_id' => $request->branch_id,
            'rol' => 0,
        ]);
        $user->roles()->detach();
        $user->assignRole($request->role);

        return new UserResource($user);
    }
    /**
     * Muestra unj usuario espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show(User $user)
    {
        return new UserResource($user->load("session")->load("branch"));
    }
    /**
     * Actualiza el registro de un susuario
     * @param  $request que se traen del body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(UserRequest $request, User $user)
    {
        $currenUser = Auth::user();
        $can_changeBranch = user_can($currenUser, "auth.changeBranch");
        $can_changeRole = user_can($currenUser, "auth.changeRole");

        if (isset($request["branch_id"])) {
            if ($user->branch_id !== $request->branch_id) {
                if (!$can_changeBranch) {
                    return response()->json([
                        "code" => "401",
                        "status" => "No authorized",
                        "message" => "This user has not permission to change branch"
                    ], 401);
                }
            }
        }

        if (isset($request['password']) && $request->password) {
            $request['password'] = Hash::make($request->password);
        }

        if (isset($request["role"])) {
            $have_role = user_can($user, $request->role);

            if (!$have_role) {
                if (!$can_changeRole) {
                    return response()->json([
                        "code" => "401",
                        "status" => "No authorized",
                        "message" => "This user has not permission to change role"
                    ], 401);
                    return [];
                }

                $user->roles()->detach();
                $user->assignRole($request->role);
                unset($request['role']);
            }
        }

        $user->update($request->all());
        return new UserResource($user);
    }
    /**
     * Elimina un usuario en espesifico.
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(User $user)
    {
        try {
            $user->delete();
            return response()->json(null, 204);
        } catch (\Throwable $th) {
            $user->deleted_at = Carbon::now();
            $user->api_token = null;
            $user->save();
            return response()->json(null, 204);
        }
    }
    /**
     * Limpia el token de un usuario
     */
    public function clearToken(User $user)
    {
        if ($user) {
            $session = Session::where('session_id', $user->id);
            if ($session) $session->delete();
            $user->api_token = null;
            $user->save();

            return response()->json([
                "success" => true
            ], 202);
        }

        return response()->json([
            "success" => false,
        ], 400);
    }
}
