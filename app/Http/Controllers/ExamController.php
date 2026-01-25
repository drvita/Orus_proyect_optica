<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Resources\Exam as ExamResources;
use App\Http\Requests\Exam as ExamRequests;
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
        $currentUser = $request->user();
        $branch = $currentUser->branch_id;
        $patient_id = $request->paciente ?? $request->patient_id ?? $request->contact_id ?? null;

        if (!$request->has('branch') || $request->branch === "all") {
            $branch = null;
        }

        if ($patient_id) {
            Log::info("[ExamController] Filter by patient: " . $patient_id);
            $contact = Contact::find($patient_id);
            if (!$contact) {
                return response()->json([
                    'message' => 'Patient not found',
                ], 404);
            }
            $request->merge(['branch_id' => null]);
        }

        $exams = $this->exam
            ->orderBy($orderby, $order)
            ->Paciente($request->search)
            ->ExamsByPaciente($patient_id)
            ->Date($date)
            ->Status($status)
            ->publish()
            ->withRelation()
            ->branch($branch)
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
        if (isset($request['age'])) {
            $request["edad"] = $request['age'];
        } else {
            $request["edad"] = $this->handleRequestToAge($request);
        }

        $exam = $this->exam->create($request->all());
        Log::info("[ExamController] exam created: " . $exam->id);
        return new ExamResources($exam);
    }

    /**
     * Muestra un examen en espesifico
     * @param  $exam id que proviene de la URL
     * @return Json api rest
     */
    public function show($id)
    {

        $exam = $this->exam::where('id', $id)
            ->withRelation()
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
        if (isset($request['branch_id'])) {
            unset($request['branch_id']);
        }

        if (isset($request['age']) && $request->age) {
            $request["edad"] = $request['age'];
        } else {
            if (!$exam->edad) {
                $request["edad"] = $this->handleRequestToAge($request);
            }
        }

        $exam->update($request->all());
        $exam->load([
            'paciente.metas',
            'user',
            'orders.nota',
            'orders.branch',
            'categoryPrimary',
            'categorySecondary',
            'branch',
            'metas'
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

    public function handleRequestToAge($request)
    {
        $patient = Contact::find($request->contact_id);
        if ($patient) {
            $metas = $patient->metas;

            if ($metas) {
                $birthday = null;
                foreach ($patient->metas as $meta) {
                    if ($meta->key === "metadata" && isset($meta->value["birthday"])) {
                        $birthday = new Carbon($meta->value["birthday"]);
                    }
                }
                if ($birthday) {
                    $request->age = $birthday->diffInYears(carbon::now());
                }
            }
        }

        return $request->age ? $request->age : 0;
    }
}
