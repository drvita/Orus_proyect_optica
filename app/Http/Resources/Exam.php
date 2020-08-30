<?php

namespace App\Http\Resources;
use App\Http\Resources\ContactShort;
use Illuminate\Http\Resources\Json\JsonResource;

class Exam extends JsonResource{

    public function toArray($request){
        $return['id'] = $this->id;
        $return['paciente'] = new ContactShort($this->paciente);
        $return['edad'] = $this->edad? $this->edad : 0;
        $return['keratometriaoi'] = $this->keratometriaoi ? $this->keratometriaoi : '';
        $return['keratometriaod'] = $this->keratometriaod ? $this->keratometriaod : '';
        $return['pantalleooi'] = $this->pantalleooi ? $this->pantalleooi : '';
        $return['pantalleood'] = $this->pantalleood ? $this->pantalleood : '';
        $return['interrogatorio'] = $this->interrogatorio ? $this->interrogatorio : '';
        $return['cefalea'] = $this->cefalea ? $this->cefalea : false;
        $return['c_frecuencia'] = $this->c_frecuencia ? $this->c_frecuencia : '';
        $return['c_intensidad'] = $this->c_intensidad ? $this->c_intensidad : 0;
        $return['frontal'] = $this->frontal ? $this->frontal : false;
        $return['temporal'] = $this->temporal ? $this->temporal : false;
        $return['occipital'] = $this->occipital ? $this->occipital : false;
        $return['generality'] = $this->generality ? $this->generality : false;
        $return['temporaoi'] = $this->temporaoi ? $this->temporaoi : false;
        $return['temporaod'] = $this->temporaod ? $this->temporaod : false;
        $return['coa'] = $this->coa ? $this->coa : "";
        $return['aopp'] = $this->aopp ? $this->aopp : "";
        $return['aopf'] = $this->aopf ? $this->aopf : "";
        $return['avsloi'] = $this->avsloi ? $this->avsloi : "";
        $return['avslod'] = $this->avslod ? $this->avslod : "";
        $return['avcgaoi'] = $this->avcgaoi ? $this->avcgaoi : "";
        $return['avcgaod'] = $this->avcgaod ? $this->avcgaod : "";
        $return['cvoi'] = $this->cvoi ? $this->cvoi : "";
        $return['cvod'] = $this->cvod ? $this->cvod : "";
        $return['oftalmoscopia'] = $this->oftalmoscopia ? $this->oftalmoscopia : "";
        $return['rsoi'] = $this->rsoi ? $this->rsoi : "";
        $return['rsod'] = $this->rsod ? $this->rsod : "";
        $return['diagnostico'] = $this->diagnostico ? $this->diagnostico : "Hemetrope";
        $return['presbicie'] = $this->presbicie ? $this->presbicie : true;
        $return['txoftalmico'] = $this->txoftalmico ? $this->txoftalmico : "";
        $return['esferaoi'] = $this->esferaoi ? $this->esferaoi : "";
        $return['esferaod'] = $this->esferaod ? $this->esferaod : "";
        $return['cilindroi'] = $this->cilindroi ? $this->cilindroi : "";
        $return['cilindrod'] = $this->cilindrod ? $this->cilindrod : "";
        $return['ejeoi'] = $this->ejeoi ? $this->ejeoi : "";
        $return['ejeod'] = $this->ejeod ? $this->ejeod : "";
        $return['adicioni'] = $this->adicioni ? $this->adicioni : "";
        $return['adiciond'] = $this->adiciond ? $this->adiciond : "";
        $return['dpoi'] = $this->dpoi ? $this->dpoi : "";
        $return['dpod'] = $this->dpod ? $this->dpod : "";
        $return['avfoi'] = $this->avfoi ? $this->avfoi : "";
        $return['avfod'] = $this->avfod ? $this->avfod : "";
        $return['avf2o'] = $this->avf2o ? $this->avf2o : "";
        $return['lcmarca'] = $this->lcmarca ? $this->lcmarca : "";
        $return['lcgoi'] = $this->lcgoi ? $this->lcgoi : "";
        $return['lcgod'] = $this->lcgod ? $this->lcgod : "";
        $return['txoptico'] = $this->txoptico ? $this->txoptico : "";
        $return['alturaoi'] = $this->alturaoi ? $this->alturaoi : "";
        $return['alturaod'] = $this->alturaod ? $this->alturaod : "";
        $return['pioi'] = $this->pioi ? $this->pioi : 0;
        $return['piod'] = $this->piod ? $this->piod : 0;
        $return['observaciones'] = $this->observaciones ? $this->observaciones : "";
        $return['pc'] = $this->pc ? $this->pc : false;
        $return['tablet'] = $this->tablet ? $this->tablet : false;
        $return['movil'] = $this->movil ? $this->movil : false;
        $return['lap'] = $this->lap ? $this->lap : false;
        $return['lap_time'] = $this->lap_time ? $this->lap_time : "";
        $return['pc_time'] = $this->pc_time ? $this->pc_time : "";
        $return['tablet_time'] = $this->tablet_time ? $this->tablet_time : "";
        $return['movil_time'] = $this->movil_time ? $this->movil_time : "";
        $return['d_time'] = $this->d_time ? $this->d_time : "";
        $return['d_media'] = $this->d_media ? $this->d_media : "";
        $return['d_test'] = $this->d_test ? $this->d_test : "";
        $return['d_fclod'] = $this->d_fclod ? $this->d_fclod : false;
        $return['d_fcloi'] = $this->d_fcloi ? $this->d_fcloi : false;
        $return['d_fclod_time'] = $this->d_fclod_time ? $this->d_fclod_time : "";
        $return['d_fcloi_time'] = $this->d_fcloi_time ? $this->d_fcloi_time : "";
        $return['estado'] = $this->status;
        $return['created'] = $this->user->name;
        $return['created_at'] = $this->created_at->format('Y-m-d H:i');
        $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        return $return;
    }
}
