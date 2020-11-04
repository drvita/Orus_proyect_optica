<?php

namespace App\Http\Controllers;
use App\Models\Atm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Atm as AtmResources;
use App\Http\Requests\Atm as AtmRequests;

class AtmController extends Controller{
    protected $atm;

    public function __construct(Atm $atm){
        $this->atm = $atm;
    }
    /**
     * Lista las entradas de caja
     * @return json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order==="desc"? "desc" : "asc";

        $atm = $this->atm
                ->orderBy($orderby, $order)
                ->Date($request->date)
                ->User($request->user, Auth::user())
                ->paginate(10);

        return AtmResources::collection($atm);
    }

    /**
     * Almacena una entrada nueva a caja
     * @param  $request datos de caja desde body en json
     * @return json api rest
     */
    public function store(AtmRequests $request){
        $request['user_id'] = Auth::user()->id;
        $atm = $this->atm->create( $request->all() );
        return new AtmResources( $atm );
    }

    /**
     * Lista una entrada en espesifico de caja
     * @param  $atm identificador del registro
     * @return json api rest
     */
    public function show(Atm $atm){
        return new AtmResources( $atm );
    }

    /**
     * Actualiza una entrada de caja
     * @param  $request datos de la caja desde body en json
     * @param  $atm identificador de la caja
     * @return json api rest
     */
    public function update(AtmRequests $request, Atm $atm){
        $request['user_id'] = $atm->user_id;
        $atm->update( $request->all() );
        return new AtmResources( $atm );
    }

    /**
     * Elimina una entrada de caja
     * @param  $atm identificador de la entrada
     * @return json api rest
     */
    public function destroy(Atm $atm){
        $atm->delete();
        return response()->json(null, 204);
    }
}
