<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use App\Models\Exam;
use Illuminate\Http\Request;
use App\Http\Resources\Exam as ExamResources;
use App\Http\Requests\Exam as ExamRequests;

class ExamController extends Controller{
    protected $exam;

    public function __construct(Exam $exam){
        $this->exam = $exam;
    }
    /**
     * Muestra una lista de examenes
     * @return Json api rest
     */
    public function index(){
        return ExamResources::collection(
            $this->exam::all()
        );
    }

    /**
     * Alacena un nuevo examen
     * @param  $request body en json
     * @return Json api rest
     */
    public function store(ExamRequests $request){
        $request['user_id']=Auth::id();
        $exam = $this->exam->create($request->all());
        return new ExamResources($exam);
    }

    /**
     * Muestra un examen en espesifico
     * @param  $exam id que proviene de la URL
     * @return Json api rest
     */
    public function show(Exam $exam){
        return new ExamResources($exam);
    }

    /**
     * Actualiza un examen en espesifico
     * @param  $request proveniente del body en json
     * @param  $exam identificador del examen
     * @return Json api rest
     */
    public function update(ExamRequests $request, Exam $exam){
        $request['user_id']=$exam->user_id;
        $exam->update( $request->all() );
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
