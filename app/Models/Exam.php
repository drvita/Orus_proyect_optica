<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Contact;
use App\User;
use App\Models\Order;
use Illuminate\Http\Request;

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
        "d_fcloi_time","status","contact_id","user_id","category_id","category_ii","adicion_media_oi",
        "adicion_media_od"
    ];
    protected $hidden = ['category_id','category_ii','user_id','contact_id'];
    protected $dates = [
        'updated_at',
        'created_at'
    ];
    //Relationships
    public function paciente(){
        return $this->belongsTo(Contact::class,'contact_id');
    }
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function user_updated(){
        return $this->belongsTo(User::class, 'updated_id', 'id');
    }
    public function orders(){
        return $this->hasMany(Order::class,'exam_id','id');
    }
    public function categoryPrimary(){
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    public function categorySecondary(){
        return $this->hasOne(Category::class, 'id', 'category_ii');
    }
    //Funciones
    //Scopes
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
    public function scopeWithRelation($query){
        $query->with('paciente','user','orders','categoryPrimary.parent.parent.parent','categorySecondary.parent.parent.parent');
    }
    public function scopePublish($query){
        $query->whereNull('deleted_at');
    }

}
