<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Resources\Exam as ExamResources;
use App\Http\Requests\Exam as ExamRequests;
use App\Models\Config;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ExamController extends Controller
{
    protected $exam;

    public function __construct(Exam $exam)
    {
        $this->middleware('can:exam.list')->only('index');
        $this->middleware('can:exam.show')->only('show');
        $this->middleware('can:exam.add')->only('store');
        $this->middleware('can:exam.edit')->only('update');
        $this->middleware('can:exam.delete')->only('destroy');
        $this->exam = $exam;
    }
    /**
     * Muestra una lista de examenes
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;
        $date = $request->date;
        $status = $request->status;
        $branch_id = $request->input("branch_id", null);
        $patient_id = $request->paciente ?? $request->patient_id ?? $request->contact_id ?? null;

        if ($branch_id === "all" || !is_numeric($branch_id)) {
            $branch_id = null;
        }

        if ($branch_id) {
            $branchExists = Config::where('name', "branches")
                ->where('id', $branch_id)
                ->exists();

            if (!$branchExists) {
                return response()->json([
                    'message' => 'Branch not found',
                ], 404);
            }
            Log::info("[ExamController] Filter by branch: " . $branch_id);
        }

        if ($patient_id) {
            $contact = Contact::find($patient_id);
            if (!$contact) {
                return response()->json([
                    'message' => 'Patient not found',
                ], 404);
            }
            Log::info("[ExamController] Filter by patient: " . $patient_id);
        }

        $exams = $this->exam
            ->orderBy($orderby, $order)
            ->Paciente($request->search)
            ->ExamsByPaciente($patient_id)
            ->Date($date)
            ->Status($status)
            ->publish()
            ->withRelation()
            ->branch($branch_id)
            ->paginate($page);

        return ExamResources::collection($exams);
    }

    /**
     * Alacena un nuevo examen
     * @param  $request body en json
     * @return Json api rest
     */
    public function store(ExamRequests $request)
    {
        if ($request->has('age') && $request->age) {
            Log::info("[ExamController] Set manual age: " . $request['age']);
            $request["edad"] = $request['age'];
        } else {
            Log::info("[ExamController] Set age from patient");
            $request["edad"] = $this->handleRequestToAge($request);
        }

        $exam = $this->exam->create($request->all());

        // Sync relationships (Dual Write Strategy)
        $this->syncExamRelations($exam, $request->all(), $request->query('version', 1));

        Log::info("[ExamController] exam created: " . $exam->id);
        return new ExamResources($exam->load(['lifestyle', 'clinical', 'functions']));
    }

    /**
     * Muestra un examen en espesifico
     * @param  $exam id que proviene de la URL
     * @return Json api rest
     */
    public function show(Request $request, $id)
    {
        $exam = $this->exam::where('id', $id)
            ->withRelation($request->input('version', 1))
            ->first();
        if ($exam->status == 0 && !$exam->started_at) {
            Log::info("[ExamController] exam started: " . $exam->id);
            $exam->update([
                "started_at" => now()
            ]);
        }

        return new ExamResources($exam);
    }

    /**
     * Actualiza un examen en espesifico
     * @param  $request proveniente del body en json
     * @param  $exam identificador del examen
     * @return Json api rest
     */
    public function update(ExamRequests $request, Exam $exam)
    {
        if ($request->has('branch_id')) {
            $request->offsetUnset('branch_id');
        }

        if ($request->has('age') && $request->age) {
            Log::info("[ExamController] Set manual age: " . $exam->id . " - " . $request['age']);
            $request["edad"] = $request['age'];
        } else {
            if (!$exam->edad) {
                $request["edad"] = $this->handleRequestToAge($request);
                Log::info("[ExamController] Set age from patient: " . $exam->id . " - " . $request["edad"]);
            } else {
                Log::info("[ExamController] Age already set: " . $exam->id . " - " . $exam->edad);
            }
        }

        $data = $request->all();
        $version = $request->query('version', 1);

        // If V2 and structured data is sent, flatten it to keep 'exams' table updated (Legacy Support)
        if ($version == 2 && $this->isStructured($data)) {
            $data = array_merge($data, $this->flattenData($data));
        }

        $exam->update($data);

        // Sync relationships (Dual Write Strategy)
        $this->syncExamRelations($exam, $data, $version);

        $exam->load([
            'paciente.metas',
            'user',
            'orders.nota',
            'orders.branch',
            'categoryPrimary',
            'categorySecondary',
            'branch',
            'metas',
            'lifestyle',
            'clinical',
            'functions'
        ]);
        Log::info("[ExamController] exam updated: " . $exam->id);
        return new ExamResources($exam);
    }

    /**
     * Elimina un examen en espesifico
     * @param  $exam identificador del examen
     * @return Json api rest
     */
    public function destroy($id)
    {
        $exam = $this->exam::where('id', $id)
            ->with('orders')
            ->first();

        $enUso = count($exam->orders);

        if ($enUso) {
            $exam->deleted_at = Carbon::now();
            $exam->updated_id = Auth::user()->id;
            $exam->save();
        } else {
            $exam->delete();
        }

        Log::info("[ExamController] exam deleted: " . $exam->id);
        return response()->json(null, 204);
    }

    public function handleRequestToAge(ExamRequests $request)
    {
        $patient = Contact::find($request->contact_id);
        return $patient ? $patient->age : 0;
    }

    /**
     * Sincroniza las relaciones 1 a 1 de examen (Lifestyle, Clinical, Functions)
     */
    private function syncExamRelations(Exam $exam, array $data, $version)
    {
        // 1. Lifestyle
        $lifestyleFields = (new \App\Models\ExamLifestyle)->getFillable();
        $lifestyleData = ($version == 2 && isset($data['lifestyle']))
            ? $data['lifestyle']
            : array_intersect_key($data, array_flip($lifestyleFields));

        if (!empty($lifestyleData)) {
            $exam->lifestyle()->updateOrCreate([], $lifestyleData);
        }

        // 2. Clinical
        $clinicalFields = (new \App\Models\ExamClinical)->getFillable();
        $clinicalData = ($version == 2 && isset($data['clinical']))
            ? $data['clinical']
            : array_intersect_key($data, array_flip($clinicalFields));

        if (!empty($clinicalData)) {
            $exam->clinical()->updateOrCreate([], $clinicalData);
        }

        // 3. Functions
        $functionsFields = (new \App\Models\ExamFunctions)->getFillable();
        $functionsData = ($version == 2 && isset($data['functions']))
            ? $data['functions']
            : array_intersect_key($data, array_flip($functionsFields));

        if (!empty($functionsData)) {
            $exam->functions()->updateOrCreate([], $functionsData);
        }

        // 4. Before Graduation (Meta)
        if (array_key_exists('before_graduation', $data)) {
            $value = $data['before_graduation'];
            if (is_null($value)) {
                Log::info("[ExamController] before_graduation is null");
                $exam->metas()->where('key', 'before_graduation')->delete();
            } else {
                $exam->metas()->updateOrCreate(
                    ['key' => 'before_graduation'],
                    ['value' => $value]
                );
            }
        }
    }

    /**
     * Valida si el JSON enviado tiene estructura de V2 (objetos anidados)
     */
    private function isStructured(array $data)
    {
        return isset($data['lifestyle']) || isset($data['clinical']) || isset($data['functions']);
    }

    /**
     * Aplana datos anidados para guardarlos en la tabla 'exams' actual
     */
    private function flattenData(array $data)
    {
        $flat = [];
        if (isset($data['lifestyle'])) $flat = array_merge($flat, $data['lifestyle']);
        if (isset($data['clinical'])) $flat = array_merge($flat, $data['clinical']);
        if (isset($data['functions'])) $flat = array_merge($flat, $data['functions']);
        return $flat;
    }
}
