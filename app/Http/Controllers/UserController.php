<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\User as UserResource;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Models\Session;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{
    public function __construct(private User $user)
    {
        $this->middleware('can:user.list')->only('index');
        $this->middleware('can:user.show')->only('show');
        $this->middleware('can:user.add')->only('store');
        $this->middleware('can:user.edit')->only('update');
        $this->middleware('can:user.delete')->only('destroy');
        $this->middleware('can:auth.closeSession')->only('clearToken');
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
        return new UserResource($user->load(["session", "branch"]));
    }
    /**
     * Actualiza el registro de un susuario
     * @param  $request que se traen del body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(UserRequest $request, User $user)
    {
        $currenUser = $request->user();
        $can_changeBranch = $currenUser->can("auth.changeBranch");
        $can_changeRole = $currenUser->can("auth.changeRole");

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
            $have_role = $user->can($request->role);

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
    /**
     * Cambia el branch_id de un usuario
     */
    public function changeBranch(Request $request)
    {
        $request->validate([
            'branch_id' => 'required|exists:config,id,name,branches'
        ]);
        $user = $request->user();

        $user->branch_id = $request->branch_id;
        $user->save();
        return new UserResource($user->load('branch', 'session'));
    }

    /**
     * Actualiza el perfil del usuario autenticado
     */
    public function updateProfile(UserRequest $request)
    {
        $user = $request->user();
        $data = $request->except(['email', 'password', 'branch_id', 'id', 'deleted_at']);
        $user->update($data);

        if ($request->has('phones')) {
            $user->phones()->delete();
            foreach ($request->phones as $phone) {
                $user->phones()->create([
                    'type' => $phone['type'] ?? 'mobile',
                    'number' => $phone['number'],
                    'country_code' => $phone['country_code'] ?? '+52',
                ]);
            }
        }

        return new UserResource($user->load('phones', 'branch', 'session'));
    }


    /**
     * Devuelve estadísticas del usuario según su rol
     */
    public function stats(Request $request)
    {
        $user = $request->user();

        if ($user->hasRole('doctor')) {
            return $this->doctorStats($user);
        }

        if ($user->hasRole('admin')) {
            return $this->adminStats();
        }

        if ($user->hasRole('ventas')) {
            return $this->ventasStats();
        }

        return response()->json([]);
    }

    private function doctorStats($user)
    {
        $today = Carbon::today();
        $startOfMonth = Carbon::now()->startOfMonth();
        $branch_id = $user->branch_id;

        // Visitas de hoy: pacientes registrados hoy o con examen hoy
        $visitsToday = \App\Models\Contact::where('type', 0)
            ->with('metas')
            ->where(function ($query) use ($today) {
                $query->whereDate('created_at', $today)
                    ->orWhereHas('exams', function ($q) use ($today) {
                        $q->whereDate('created_at', $today);
                    });
            })
            ->orderByDesc('updated_at')
            ->get(['id', 'name', 'updated_at']);

        // Exámenes pendientes (status 0)
        $pendingExams = \App\Models\Exam::where('status', 0)
            ->where('branch_id', $branch_id)
            ->with('paciente:id,name')
            ->orderByDesc('created_at')
            ->get(['id', 'contact_id', 'created_at']);

        $pendingExamsFormatted = $pendingExams->map(function ($exam) {
            return [
                'id' => $exam->id,
                'paciente' => $exam->paciente->name ?? 'N/A',
                'gender' => $exam->paciente->gender,
                'age' => $exam->paciente->age,
                'created_at' => $exam->created_at->format('Y-m-d H:i')
            ];
        });

        // Pacientes nuevos del mes
        $newPatientsMonth = \App\Models\Contact::where('type', 0)
            ->with('metas')
            ->whereDate('created_at', '>=', $startOfMonth)
            ->orderByDesc('created_at')
            ->get(['id', 'name', 'created_at']);

        return response()->json([
            'visitsToday' => $visitsToday->map(fn($v) => [
                'id' => $v->id,
                'paciente' => $v->name,
                'gender' => $v->gender,
                'age' => $v->age,
                'updated_at' => $v->updated_at->format('Y-m-d H:i')
            ]),
            'pendingExams' => $pendingExamsFormatted,
            'newPatientsMonth' => $newPatientsMonth->map(fn($p) => [
                'id' => $p->id,
                'paciente' => $p->name,
                'gender' => $p->gender,
                'age' => $p->age,
                'created_at' => $p->created_at->format('Y-m-d H:i')
            ]),
        ]);
    }

    private function adminStats()
    {
        return response()->json([]);
    }

    private function ventasStats()
    {
        return response()->json([]);
    }

    /**
     * Genera un código para vincular con Telegram u otra red social.
     * key: red social, value: AAA-00000
     */
    public function generateSocialCode(Request $request)
    {
        $request->validate([
            'network' => 'required|string|in:' . implode(',', User::SOCIAL_CHANNELS)
        ]);

        $user = $request->user();
        $network = $request->network;

        // Generar código: 3 letras - 5 números
        $code = Str::upper(Str::random(3)) . '-' . rand(10000, 99999);

        // Buscar si ya existe la meta para este network y actualizarla o crearla
        $user->metas()->updateOrCreate(
            ['key' => $network],
            ['value' => $code]
        );
        Log::info("Social code generated for user: {$user->email} / {$network}");
        return new UserResource($user->load('metas'));
    }

    /**
     * Elimina el vínculo con la red social.
     */
    public function deleteSocialCode(Request $request)
    {
        $request->validate([
            'network' => 'required|string|in:' . implode(',', User::SOCIAL_CHANNELS) . ',all'
        ]);

        $user = $request->user();
        $network = $request->network;

        if ($network === 'all') {
            $user->metas()->whereIn('key', User::SOCIAL_CHANNELS)->delete();
        } else {
            $user->metas()->where('key', $network)->delete();
        }

        Log::info("Social code deleted for user: {$user->email} / {$network}");
        return response()->json([
            'success' => true,
            'message' => 'Vínculo eliminado correctamente'
        ]);
    }
}
