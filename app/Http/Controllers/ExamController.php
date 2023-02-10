<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\Models\Contact;
use Illuminate\Http\Request;
use App\Http\Resources\Exam as ExamResources;
use App\Http\Requests\Exam as ExamRequests;
use Carbon\Carbon;

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
        $currentUser = Auth::user();
        $branchUser = $currentUser->branch_id;
        $branch = $branchUser;

        // If branches var is not present, use the same branch of user
        if (isset($request->branch)) {
            if ($request->branch === "all") {
                $branch = null;
            } else {
                $branch = $request->branch;
            }
        }

        $exams = $this->exam
            ->orderBy($orderby, $order)
            ->Paciente($request->search)
            ->ExamsByPaciente($request->paciente)
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
        $currentUser = Auth::user();
        $request['user_id'] = $currentUser->id;
        $request['branch_id'] = $currentUser->branch_id;
        $request['status'] = 0;
        if (isset($request['age'])) {
            $request["edad"] = $request['age'];
        } else {
            $request["edad"] = $this->handleRequestToAge($request);
        }

        $exam = $this->exam->create($request->all());

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
        $currentUser = Auth::user();
        $request['updated_id'] = $currentUser->id;

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
