<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResources;
use App\Http\Requests\Order as OrderRequests;
use App\Events\OrderSave;
use App\Http\Resources\OrderActivity;
use Carbon\Carbon;

class OrderController extends Controller
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->middleware('can:order.list')->only('index');
        $this->middleware('can:order.show')->only('show');
        $this->middleware('can:order.add')->only('store');
        $this->middleware('can:order.edit')->only('update');
        $this->middleware('can:order.delete')->only('destroy');
        $this->order = $order;
    }
    /**
     * @OA\Get(
     *  path="/api/orders",
     *  summary="List of orders",
     *  description="GET list of orders in DB",
     *  operationId="index",
     *  tags={"Orders"},
     *  security={ {"bearer": {} }},
     *  @OA\Parameter(
     *      name="email",
     *      in="query",
     *      required=false,
     *      @OA\Schema(
     *           type="string"
     *      )
     *  ),
     *  @OA\Response(
     *    response=401,
     *    description="Wrong credentials response",
     *    @OA\JsonContent(
     *       @OA\Property(property="message", type="string", example="Sorry. Please try again")
     *        )
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
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 20;
        $currentUser = Auth::user();
        $branchUser = $currentUser->branch_id;
        $branch = $branchUser;

        // If branches var is not present, use the same branch of user
        // only admin can see all branches
        if (isset($request->branch)) {
            if ($request->branch === "all") {
                $branch = null;
            } else {
                $branch = $request->branch;
            }
        }

        $orderdb = $this->order
            ->withRelation()
            ->Estado($request->status)
            ->orderBy($orderby, $order)
            ->Paciente($request->search)
            ->SearchId($request->search)
            ->publish()
            ->branch($branch)
            ->paginate($page);

        return OrderResources::collection($orderdb);
    }

    /**
     * Almacena una nueva orden de pedido
     * @param  $request de body en Json
     * @return Json api rest
     */
    public function store(OrderRequests $request)
    {
        $order = $this->order->create($request->all());

        if ($request->has("items")) {
            $currentUser = Auth::user();
            $order->items = getItemsRequest($request->items, $currentUser->branch_id);
        }

        if ($request->has("sale")) {
            $order->sale = $request->sale;
        }

        if (count($order->items)) event(new OrderSave($order, false));

        $order = $this->order->find($order->id);
        return new OrderActivity($order);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Order $order)
    {
        $order->withRelation();
        return new OrderActivity($order);
    }

    /**
     * Actualiza una orden espesifica
     * @param  $request datos a actualizar por medio de body en Json
     * @param  $order identificador de la orden a actualizar
     * @return Json api rest
     */
    public function update(OrderRequests $request, Order $order)
    {
        $auth = Auth::user();
        $request['updated_id'] = $auth->id;
        $data = ["status" => $request->status];

        if ($request->status == 1) {
            $data["lab_id"] = $request->lab_id;
            $data["npedidolab"] = $request->lab_order;
        } else if ($request->status == 2) {
            $data["ncaja"] = $request->bi_box;
            $data["observaciones"] = $request->bi_details;
        }

        $order->update($data);

        return new OrderActivity($order);
    }

    /**
     * Elimina una orden
     * @param  $order identificador de la orden
     * @return null 404
     */
    public function destroy(Order $order)
    {
        if ($order->nota()) {
            $order->deleted_at = Carbon::now();
            $order->updated_id = Auth::user()->id;
            $order->save();
        } else {
            $order->delete();
        }

        return response()->json(null, 204);
    }
}
