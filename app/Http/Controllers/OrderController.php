<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Order as OrderResources;
use App\Http\Requests\Order as OrderRequests;
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
        $status = $request->status ?? null;
        $examStatus = $request->exam_status ?? null;
        $patientName = $request->patient_name ?? null;

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
            ->status($status)
            ->patient($patient_id)
            ->patientName($patientName)
            ->search($request->search)
            ->branch($branch_id)
            ->examStatus($examStatus)
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
            \App\Jobs\ProcessOrderItems::dispatchSync($order, $request->items, $request->user());
        }
        if ($request->has("sale")) {
            \App\Jobs\ProcessOrderSale::dispatchSync($order, $request->sale, $request->user());
        }

        $order->refresh();

        return new OrderActivity($order);
    }

    /**
     * Muestra una orden en espesifico
     * @param  $order identificador de la orden
     * @return Json api rest
     */
    public function show(Order $order)
    {
        $order->load([
            'examen',
            'paciente.phones',
            'laboratorio',
            'user',
            'user_updated',
            'nota',
            'items.lot',
            'items.item.categoria.parent.parent.parent.parent.parent'
        ]);
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
        $currentStatus = $order->status ?? 0;

        if ($currentStatus == Order::STATUS_DELIVERED || $currentStatus == Order::STATUS_CANCELLED) {
            return response()->json(['message' => 'No se puede editar una orden con este estatus'], 400);
        }

        $data = ["status" => $request->status ?? $currentStatus];
        $version = $request->input("version", 1);
        $currentItems = $order->items;

        if ($request->status == 1) {
            $data["lab_id"] = $request->lab_id;
            $data["npedidolab"] = $request->lab_order ?? $request->npedidolab;
            Log::info("[OrderController.update] Update data to lab: " . $order->id, $request->all());
        } else if ($request->status == 2) {
            $data["ncaja"] = $request->bi_box;
            $data["observaciones"] = $request->bi_details;
            Log::info("[OrderController.update] Update data to bi: " . $order->id);
        } else if ($request->status == 5) {
            $obs = $request->observaciones;
            if (!empty($order->observaciones)) {
                $data["observaciones"] = $order->observaciones . "\nmotivo de cancelacion: " . $obs;
            } else {
                $data["observaciones"] = $obs;
            }
            Log::info("[OrderController.update] Order cancelled: " . $order->id, ["reason" => $obs]);
        }
        $order->update($data);

        if ($version == 2) {
            if ($request->has("items") && ($currentStatus == 0 || !$currentItems->count())) {
                Log::info("[OrderController.update] Order processing items: " . $order->id);
                \App\Jobs\ProcessOrderItems::dispatchSync($order, $request->items, $request->user());
            }
            if ($request->has("sale") && in_array($currentStatus, [0, 3])) {
                Log::info("[OrderController.update] Order processing sale: " . $order->id);
                \App\Jobs\ProcessOrderSale::dispatchSync($order, $request->sale, $request->user());
            }
            if ($request->has("items") && $currentStatus == 1) {
                Log::info("[OrderController.update] Order updating items: " . $order->id);
                \App\Jobs\ProcessOrderItemsUpdate::dispatchSync($order, $request->items, $request->user());
            }

            $order->refresh();
        }

        return new OrderActivity($order->load([
            'items',
            'paciente',
            'examen',
            'laboratorio',
            'nota',
            'branch'
        ]));
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
