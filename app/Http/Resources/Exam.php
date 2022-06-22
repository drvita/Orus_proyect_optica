<?php

namespace App\Http\Resources;

use App\Http\Resources\ContactInExam as ContactResource;
use App\Http\Resources\UserInExam as UserResource;
use App\Http\Resources\OrderInExam as OrderResource;
use App\Http\Resources\CategoryInExam as CategoryResource;

use Illuminate\Http\Resources\Json\JsonResource;

class Exam extends JsonResource
{

    public function toArray($request)
    {

        $return = [];

        if (isset($this->id)) {
            $activity = $this->metas()->where("key", ["updated", "deleted", "created"])->orderBy("id", "desc")->get();

            $obj = [
                'id' => 0,
                'key' => 'created',
                'value' => json_encode([
                    "datetime" => $this->created_at,
                    "user_id" => $this->user->id
                ])
            ];
            $obj = json_decode(json_encode($obj), false);
            $obj->value = json_decode($obj->value, true);
            $activity->push($obj);

            $return['id'] = $this->id;
            $return['age'] = $this->edad ? $this->edad : 0;
            $return['keratometriaoi'] = $this->keratometriaoi ? $this->keratometriaoi : '';
            $return['keratometriaod'] = $this->keratometriaod ? $this->keratometriaod : '';
            $return['pantalleooi'] = $this->pantalleooi ? $this->pantalleooi : '';
            $return['pantalleood'] = $this->pantalleood ? $this->pantalleood : '';
            $return['interrogatorio'] = $this->interrogatorio ? $this->interrogatorio : '';
            $return['cefalea'] = $this->cefalea ? true : false;
            $return['c_frecuencia'] = $this->c_frecuencia ? $this->c_frecuencia : '';
            $return['c_intensidad'] = $this->c_intensidad ? $this->c_intensidad : 0;
            $return['frontal'] = $this->frontal ? true : false;
            $return['temporal'] = $this->temporal ? true : false;
            $return['occipital'] = $this->occipital ? true : false;
            $return['generality'] = $this->generality ? true : false;
            $return['temporaoi'] = $this->temporaoi ? true : false;
            $return['temporaod'] = $this->temporaod ? true : false;
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
            $return['presbicie'] = $this->presbicie ? true : false;
            $return['txoftalmico'] = $this->txoftalmico ? $this->txoftalmico : "";
            $return['esferaoi'] = $this->esferaoi ? $this->esferaoi : 0;
            $return['esferaod'] = $this->esferaod ? $this->esferaod : 0;
            $return['cilindroi'] = $this->cilindroi ? $this->cilindroi : 0;
            $return['cilindrod'] = $this->cilindrod ? $this->cilindrod : 0;
            $return['ejeoi'] = $this->ejeoi ? $this->ejeoi : 0;
            $return['ejeod'] = $this->ejeod ? $this->ejeod : 0;
            $return['adicioni'] = $this->adicioni ? $this->adicioni : 0;
            $return['adiciond'] = $this->adiciond ? $this->adiciond : 0;
            $return['adicion_media_od'] = $this->adicion_media_od ? $this->adicion_media_od : 0;
            $return['adicion_media_oi'] = $this->adicion_media_oi ? $this->adicion_media_oi : 0;
            $return['dpoi'] = $this->dpoi ? $this->dpoi : 0;
            $return['dpod'] = $this->dpod ? $this->dpod : 0;
            $return['avfoi'] = $this->avfoi ? $this->avfoi : "";
            $return['avfod'] = $this->avfod ? $this->avfod : "";
            $return['avf2o'] = $this->avf2o ? $this->avf2o : "";
            $return['lcmarca'] = $this->lcmarca ? $this->lcmarca : "";
            $return['lcgoi'] = $this->lcgoi ? $this->lcgoi : "";
            $return['lcgod'] = $this->lcgod ? $this->lcgod : "";
            $return['txoptico'] = $this->txoptico ? $this->txoptico : "";
            $return['alturaoi'] = $this->alturaoi ? $this->alturaoi : 0;
            $return['alturaod'] = $this->alturaod ? $this->alturaod : 0;
            $return['pioi'] = $this->pioi ? $this->pioi : 0;
            $return['piod'] = $this->piod ? $this->piod : 0;
            $return['observaciones'] = $this->observaciones ? $this->observaciones : "";
            $return['pc'] = $this->pc ? true : false;
            $return['tablet'] = $this->tablet ? true : false;
            $return['movil'] = $this->movil ? true : false;
            $return['lap'] = $this->lap ? true : false;
            $return['lap_time'] = $this->lap_time ? $this->lap_time : "00:00";
            $return['pc_time'] = $this->pc_time ? $this->pc_time : "00:00";
            $return['tablet_time'] = $this->tablet_time ? $this->tablet_time : "00:00";
            $return['movil_time'] = $this->movil_time ? $this->movil_time : "00:00";
            $return['d_time'] = $this->d_time ? $this->d_time : "00:00";
            $return['d_media'] = $this->d_media ? $this->d_media : "";
            $return['d_test'] = $this->d_test ? $this->d_test : "";
            $return['d_fclod'] = $this->d_fclod ? true : false;
            $return['d_fcloi'] = $this->d_fcloi ? true : false;
            $return['d_fclod_time'] = $this->d_fclod_time ? $this->d_fclod_time : "00:00";
            $return['d_fcloi_time'] = $this->d_fcloi_time ? $this->d_fcloi_time : "00:00";

            $return['status'] = $this->status;
            $return['category_id'] = new CategoryResource($this->categoryPrimary);
            $return['category_ii'] = new CategoryResource($this->categorySecondary);
            $return['customer'] = new ContactSimple($this->paciente);
            $return['orders'] = OrderResource::collection($this->orders);
            $return['branch'] = new ConfigBranch($this->branch);
            $return["activity"] = MetasDetails::collection($activity);

            $return['created_at'] = $this->created_at->format('Y-m-d H:i');
            $return['updated_at'] = $this->updated_at->format('Y-m-d H:i');
        }


        return $return;
    }
}
