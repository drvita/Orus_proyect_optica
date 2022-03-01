<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Payment as PaymentResources;
use App\Http\Requests\Payment as PaymentRequests;
use App\Http\Resources\PaymentBankDetails;
use App\Http\Resources\PaymentMethods;
use Carbon\Carbon;

class PaymentController extends Controller
{
    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    /**
     * @OA\Get(
     *  path="/api/payments",
     *  summary="Payments items and amounts",
     *  description="GET items or amounts that depend of parameter type.",
     *  operationId="index",
     *  tags={"Payments"},
     *  security={ {"bearer": {} }},
     *  @OA\Parameter(name="orderby",in="query",required=false,@OA\Schema(type="date")),
     *  @OA\Parameter(name="order",in="query",required=false,@OA\Schema(type="date")),
     *  @OA\Parameter(name="itemsPage",in="query",required=false,@OA\Schema(type="date")),
     *  @OA\Parameter(name="date_start",in="query",required=false,@OA\Schema(type="date")),
     *  @OA\Parameter(name="date_end",in="query",required=false,@OA\Schema(type="date")),
     *  @OA\Parameter(name="user",in="query",required=false,@OA\Schema(type="number")),
     *  @OA\Parameter(name="branch_id",in="query",required=false,@OA\Schema(type="number")),
     *  @OA\Parameter(name="type",in="query",required=false,@OA\Schema(type="string")),
     * 
     *  @OA\Response(response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry. Please try again")
     *    )
     *  ),
     *  @OA\Response(
     *     response=200,
     *     description="Success",
     *     @OA\JsonContent(
     *        @OA\Property(property="data", type="object"),
     *     )
     *  ),
     * )
     *
     * @param "" $request
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order === "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        if ($request->type && $request->type === "methods") {
            return $this->byMethod($request);
        }

        if ($request->type && $request->type === "banks") {
            return $this->byBanks($request);
        }

        $payment = $this->payment
            ->Sale($request->sale)
            ->Date($request->date)
            ->orderBy($orderby, $order)
            // TODO: protect get data if not admin
            ->User(Auth::user(), $request->user)
            ->publish()
            ->paginate($page);

        return PaymentResources::collection($payment);
    }

    /**
     * Almacena una nueva orden de pedido
     * @param  $request de body en Json
     * @return Json api rest
     */
    public function store(PaymentRequests $request)
    {
        $currentUser = Auth::user();
        $request['user_id'] = $currentUser->id;
        $request['branch_id'] = $currentUser->branch_id;
        $rolUser = $currentUser->rol;

        //Only admin can save in differents branches
        if (!$rolUser) {
            if (isset($request->branch_id)) $request['branch_id'] = $request->branch_id;
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
    public function show(Payment $payment)
    {
        return new PaymentResources($payment);
    }

    /**
     * Actualiza una orden espesifica
     * @param  $request datos a actualizar por medio de body en Json
     * @param  $order identificador de la orden a actualizar
     * @return Json api rest
     */
    public function update(PaymentRequests $request, Payment $payment)
    {
        $currentUser = Auth::user();
        $request['updated_id'] = $currentUser->id;
        $rolUser = $currentUser->rol;
        //Only admin can modify branches
        if (isset($request->branch_id) && $rolUser) {
            unset($request['branch_id']);
        }

        if ($payment) {
            $payment->update($request->all());
        }

        return new PaymentResources($payment);
    }

    /**
     * Elimina un pago
     * @param  $payment identificador del pago
     * @return null 404
     */
    public function destroy(Payment $payment)
    {
        $payment->deleted_at = Carbon::now();
        $payment->updated_id = Auth::user()->id;
        $payment->save();
        //$payment->delete();
        return response()->json(null, 204);
    }


    public function byMethod($request)
    {
        $currentUser = Auth::user();
        $dates = $this->handleDates($request);

        $payment = $this->payment
            ->methodPay()
            ->protected($currentUser, $request->user)
            ->dateStart($dates->start)
            ->dateFinish($dates->end)
            ->branchId($request->branch_id)
            ->publish();

        return PaymentMethods::collection($payment->get());
    }

    public function byBanks($request)
    {
        $currentUser = Auth::user();
        $dates = $this->handleDates($request);

        $payment = $this->payment
            ->bankDetails()
            ->publish()
            ->protected($currentUser, $request->user)
            ->dateStart($dates->start)
            ->dateFinish($dates->end)
            ->branchId($request->branch_id)
            ->with("bankName")
            ->publish();

        return PaymentBankDetails::collection($payment->get());
    }

    private function handleDates($request)
    {
        $today = Carbon::now("America/Mexico_City");
        $starDate = $request->date_start ? Carbon::parse($request->date_start, "America/Mexico_City") : $today;
        $endDate =  $today;
        $obj = [];

        if ($starDate->lt($today)) {
            $endDate = $request->date_end ? Carbon::parse($request->date_end, "America/Mexico_City") : $today;

            if ($endDate->lt($starDate)) {
                $endDate = $today;
            }

            if ($endDate->gt($today)) {
                $endDate = $today;
            }
        }

        $obj["start"] = $starDate;
        $obj["end"] = $endDate;

        return (object) $obj;
    }
}