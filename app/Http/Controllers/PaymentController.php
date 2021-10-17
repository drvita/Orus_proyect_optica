<?php

namespace App\Http\Controllers;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Payment as PaymentResources;
use App\Http\Requests\Payment as PaymentRequests;
use Carbon\Carbon;

class PaymentController extends Controller{
    protected $payment;

    public function __construct(Payment $payment){
        $this->payment = $payment;
    }
    /**
     * Muestra lista de ordenes
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order==="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $payment = $this->payment
                ->Sale($request->sale)
                ->Date($request->date)
                ->orderBy($orderby, $order)
                ->User(Auth::user(), $request->user)
                ->publish()
                ->paginate($page);

        return PaymentResources::collection($payment);
    }

    /*
    * Muestra la venta del dia
    * @Return string
    */
    public function saleday(Request $request){
        $payment = $this->payment
            ->MethodPay($request->date, Auth::user(), $request->user)
            ->get();
        return $payment;
    }
    /*
    * Muestra el detallado de pagos bancarios
    * @Return string
    */
    public function bankdetails(Request $request){
        $payment = $this->payment
            ->BankDetails($request->date, Auth::user(), $request->user)
            ->get();
        return $payment;
    }

    /**
     * Almacena una nueva orden de pedido
     * @param  $request de body en Json
     * @return Json api rest
     */
    public function store(PaymentRequests $request){
        $currentUser = Auth::user();
        $request['user_id']= $currentUser->id;
        $request['branch_id'] = $currentUser->branch_id;
        $rolUser = $currentUser->rol;

        //Only admin can save in differents branches
        if(!$rolUser){
            if(isset($request->branch_id)) $request['branch_id'] = $request->branch_id; 
        }
        
        $payment = $this->payment->create($request->all());
        //event(new PaymentSave($payment, $messegeId, $table));
        return new PaymentResources($payment);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Payment $payment){
        return new PaymentResources($payment);
    }

    /**
     * Actualiza una orden espesifica
     * @param  $request datos a actualizar por medio de body en Json
     * @param  $order identificador de la orden a actualizar
     * @return Json api rest
     */
    public function update(PaymentRequests $request, Payment $payment){
        $currentUser = Auth::user();
        $request['updated_id']= $currentUser->id;
        $rolUser = $currentUser->rol;
        //Only admin can modify branches
        if(isset($request->branch_id) && $rolUser){
            unset($request['branch_id']);
        }

        if($payment){
            $payment->update( $request->all() );
        }
        
        return New PaymentResources($payment);
    }

    /**
     * Elimina un pago
     * @param  $payment identificador del pago
     * @return null 404
     */
    public function destroy($id){
        $payment = $this->payment::where('id', $id)->first();

        $payment->deleted_at = Carbon::now();
        $payment->updated_id = Auth::user()->id;
        $payment->save();
        //$payment->delete();
        return response()->json(null, 204);
    }
}