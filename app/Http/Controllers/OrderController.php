<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResources;
use App\Http\Requests\Order as OrderRequests;
use App\Events\OrderSave;
use App\Http\Resources\OrderActivity;
use App\Models\Config;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

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
     * @param Request $request
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(Request $request)
    {
        $orderby = $request->input("orderby", "created_at");
        $order = $request->input("order", "desc");
        $page = $request->input("itemsPage", 20);
        $currentUser = $request->user();
        $branch_id = $request->input("branch_id", $currentUser->branch_id);
        $patient_id = $request->paciente ?? $request->patient_id ?? $request->contact_id ?? null;

        if ($branch_id === "all" || !is_numeric($branch_id)) {
            $branch_id = null;
        }

        if ($branch_id) {
            $branchExists = Config::where('name', "branches")
                ->where('id', $branch_id)
                ->exists();

            if (!$branchExists) {
                return response()->json([
                    'message' => 'Branch not found',
                ], 404);
            }
            Log::info("[OrderController] Filter orders by branch: " . $branch_id);
        }

        if ($patient_id) {
            $contact = Contact::find($patient_id);
            if (!$contact) {
                return response()->json([
                    'message' => 'Patient not found',
                ], 404);
            }
            Log::info("[ExamController] Filter exams by patient: " . $patient_id);
        }

        $orderdb = $this->order
            ->withRelation()
            ->orderBy($orderby, $order)
            ->status($request->status)
            ->patient($patient_id)
            ->search($request->search)
            ->branch($branch_id)
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

        if ($request->has(['items', 'sale'])) {
            if ($request->has("items")) {
                $currentUser = Auth::user();
                $order->items = getItemsRequest($request->items, $currentUser->branch_id);
            }

            if ($request->has("sale")) {
                $order->sale = $request->sale;
            }

            if (count($order->items)) {
                event(new OrderSave($order, false));
            }
        }

        Log::info("[OrderController] Order created: " . $order->id);
        return new OrderActivity($order);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Order $order)
    {
        $order->load(['examen', 'paciente.phones', 'laboratorio', 'user', 'nota', 'items']);
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
        Log::info("[OrderController] Order updated: " . $order->id);
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
        Log::info("[OrderController] Order deleted: " . $order->id);
        return response()->json(null, 204);
    }
}
