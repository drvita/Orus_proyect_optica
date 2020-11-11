<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model{
    protected $table = "exams";
    protected $fillable = [
        "edad","keratometriaoi","keratometriaod","pantalleooi","pantalleood","interrogatorio",
        "cefalea","c_frecuencia","c_intensidad","frontal","temporal","occipital","generality",
        "temporaoi","temporaod","coa","aopp","aopf","avsloi","avslod","avcgaoi","avcgaod",
        "cvoi","cvod","oftalmoscopia","rsoi","rsod","diagnostico","presbicie","txoftalmico",
        "esferaoi","esferaod","cilindroi","cilindrod","ejeoi","ejeod","adicioni","adiciond",
        "dpoi","dpod","avfod","avfoi","avf2o","lcmarca","lcgoi","lcgod","txoptico","alturaod",
        "alturaoi","pioi","piod","observaciones","pc","tablet","movil","lap","lap_time","pc_time",
        "tablet_time","movil_time","d_time","d_media","d_test","d_fclod","d_fcloi","d_fclod_time",
        "d_fcloi_time","status","contact_id","user_id", "category_id"
    ];
    protected $hidden = [];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    public function paciente(){
        return $this->belongsTo('App\Models\Contact','contact_id');
    }
    public function user(){
        return $this->belongsTo('App\User');
    }
    public function orders(){
        return $this->belongsTo('App\Models\Order','id','exam_id');
    }
    public function scopePaciente($query, $name){
        if(trim($name) != ""){
            $query->whereHas('paciente', function($query) use ($name){
                $query->where('name',"LIKE","%$name%");
            });
        }
    }
    public function scopeExamsByPaciente($query, $search){
        if(trim($search) != ""){
            $query->where("contact_id",$search);
        }
    }
    public function scopeDate($query, $search){
        if(trim($search) != ""){
            $query->WhereDate("created_at",$search);
        }
    }
    public function scopeStatus($query, $search){
        if(trim($search) != ""){
            $query->orWhere("status",$search);
        }
    }

}
