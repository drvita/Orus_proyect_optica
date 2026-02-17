<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Messenger as MessengerResource;
use App\Http\Requests\Messenger as MessengerRequests;
use App\Models\Messenger;
use Illuminate\Support\Facades\Auth;

class MessengerController extends Controller
{
    protected $message;

    public function __construct(\App\Models\Message $message)
    {
        $this->middleware('can:messenger.list')->only('index');
        $this->middleware('can:messenger.show')->only('show');
        $this->middleware('can:messenger.add')->only('store');
        $this->middleware('can:messenger.edit')->only('update');
        $this->message = $message;
    }

    /**
     * Muestra la lista de mensajes
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $version = $request->query('version', 1);
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order === "asc" ? "asc" : "desc";

        $query = $this->message->orderBy($orderby, $order);

        if ($version == 2) {
            $table = $request->query('table', 'contacts');
            $idRow = $request->query('idRow');

            if (!$idRow) {
                return response()->json(['error' => 'idRow is required for version 2'], 422);
            }

            if (!array_key_exists($table, \App\Models\Message::TABLE_MAP)) {
                return response()->json(['error' => 'tabla no valida'], 400);
            }

            $type = \App\Models\Message::TABLE_MAP[$table];
            $query->where('messagable_type', $type)->where('messagable_id', $idRow);
        } else {
            // Version 1: Translate legacy fields to polymorphic
            $table = $request->query('table');
            $idRow = $request->query('idRow');

            if ($table && $idRow) {
                if ($table === 'orders') {
                    $order = \App\Models\Order::find($idRow);
                    if ($order) {
                        $query->where('messagable_type', 'App\Models\Contact')
                            ->where('messagable_id', $order->contact_id);
                    } else {
                        return response()->json(['error' => 'Order not found'], 404);
                    }
                } elseif (array_key_exists($table, \App\Models\Message::TABLE_MAP)) {
                    $query->where('messagable_type', \App\Models\Message::TABLE_MAP[$table])
                        ->where('messagable_id', $idRow);
                }
            }
        }

        $messages = $query->paginate($request->input('itemsPage', 50));
        return MessengerResource::collection($messages);
    }

    /**
     * Registra un nuevo mensaje.
     * @param  $request que se traen de post body json
     * @return Json api rest
     */
    public function store(MessengerRequests $request)
    {
        $version = $request->query('version', 1);
        $data = $request->all();
        $data['user_id'] = Auth::user()->id;

        if ($version == 2) {
            $table = $request->input('table', 'contacts');
            $idRow = $request->idRow;

            if (!array_key_exists($table, \App\Models\Message::TABLE_MAP)) {
                return response()->json(['error' => 'tabla no valida'], 400);
            }

            $data['messagable_type'] = \App\Models\Message::TABLE_MAP[$table];
            $data['messagable_id'] = $idRow;
        } else {
            // Version 1: Translate legacy fields to polymorphic
            $table = $request->input('table');
            $idRow = $request->idRow;

            if ($table && $idRow) {
                if ($table === 'orders') {
                    $order = \App\Models\Order::find($idRow);
                    if ($order) {
                        $data['messagable_type'] = 'App\Models\Contact';
                        $data['messagable_id'] = $order->contact_id;
                    } else {
                        return response()->json(['error' => 'Order not found'], 404);
                    }
                } elseif (array_key_exists($table, \App\Models\Message::TABLE_MAP)) {
                    $data['messagable_type'] = \App\Models\Message::TABLE_MAP[$table];
                    $data['messagable_id'] = $idRow;
                }
            }
        }

        $message = $this->message->create($data);
        return new MessengerResource($message);
    }

    /**
     * Muestra un mensaje especifico (V1 only)
     * @param  int  $id
     * @return Json api rest
     */
    public function show(\App\Models\Message $messanger)
    {
        if (request()->query('version') == 2) {
            return response()->json(['error' => 'Show method not available for version 2'], 404);
        }
        return new MessengerResource($messanger);
    }

    /**
     * Actualiza el registro de un mensaje
     * @param  $request que se traen del body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(MessengerRequests $request, \App\Models\Message $messanger)
    {
        $messanger->update($request->all());
        return new MessengerResource($messanger);
    }

    /**
     * Elimina un mensaje.
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(\App\Models\Message $message)
    {
        $message->delete();
        return response()->json(null, 204);
    }
}
