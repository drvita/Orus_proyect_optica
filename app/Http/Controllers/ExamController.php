<?php

namespace App\Http\Controllers;

use App\Events\ExamEvent;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use App\User;
use Illuminate\Http\Request;
use App\Http\Resources\Exam as ExamResources;
use App\Http\Requests\Exam as ExamRequests;
use App\Notifications\ExamNotification;

class ExamController extends Controller{
    protected $exam;

    public function __construct(Exam $exam){
        $this->exam = $exam;
    }
    /**
     * Muestra una lista de examenes
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;
        $date = $request->date;
        $status = $request->status;

        $exams = $this->exam
            ->orderBy($orderby, $order)
            ->Paciente($request->search)
            ->ExamsByPaciente($request->paciente)
            ->Date($date)
            ->Status($status)
            ->paginate($page);

        return ExamResources::collection($exams);
    }

    /**
     * Alacena un nuevo examen
     * @param  $request body en json
     * @return Json api rest
     */
    public function store(ExamRequests $request){
        $request['user_id']= Auth::user()->id;
        $rol = Auth::user()->rol;
        //Cuando se crea no se puede cerrrar el examen
        $request['status']= 0;
        $exam = $this->exam->create($request->all());
        //Si es vendedora hay que notificar al medico
        if($rol === 1) event(new ExamEvent($exam, $rol));
        return new ExamResources($exam);
    }

    /**
     * Muestra un examen en espesifico
     * @param  $exam id que proviene de la URL
     * @return Json api rest
     */
    public function show($id){
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
    public function update(Request $request, Exam $exam){
        $request['user_id']=Auth::user()->id;
        //$status = $exam->status;
        $rol = Auth::user()->rol;
        //Vendedores no pueden modificar el estado
        //if($rol !== 2) $request['status']= $status;
        $exam->update( $request->all() );
        //Si es medico y estaba en no terminado y cambio a terminado
        //if($rol === 2 && !$status && $exam->status) event(new ExamEvent($exam, $rol));
        return New ExamResources($exam);
    }

    /**
     * Elimina un examen en espesifico
     * @param  $exam identificador del examen
     * @return Json api rest
     */
    public function destroy(Exam $exam){
        $exam->delete();
        return response()->json(null, 204);
    }
}
