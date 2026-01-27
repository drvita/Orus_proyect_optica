<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Resources\Contact as ContactResource;
use App\Http\Resources\ContactList as ContactResourceList;
use App\Http\Resources\ContactShowV2;
use App\Http\Requests\ContactRequest;
use App\Models\Contact;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ContactController extends Controller
{
    protected $contact;

    public function __construct(Contact $contact)
    {
        $this->middleware('can:contact.list')->only('index');
        $this->middleware('can:contact.show')->only('show');
        $this->middleware('can:contact.add')->only('store');
        $this->middleware('can:contact.edit')->only('update');
        $this->middleware('can:contact.delete')->only('destroy');
        $this->contact = $contact;
    }
    /**
     * Muestra la lista de contactos
     * @return Json api rest
     */
    public function index(Request $request)
    {
        $orderby = $request->input('orderby', 'created_at');
        $order = $request->input('order', 'desc');
        $page = $request->input('itemsPage', 50);
        $version = $request->input('version', 'v1');
        $search = $request->input('search', '');
        $type = $request->input('type', '');
        $business = $request->input('business', '');
        $name = $request->input('name', '');
        $email = $request->input('email', '');
        $except = $request->input('except');

        if ($version == 'v1') {
            $contacts = $this->contact->withRelation();
        } else {
            $contacts = $this->contact->withRelationShort();
        }

        $contacts = $contacts->withUsageCounts()
            ->orderBy($orderby, $order)
            ->searchUser($search)
            ->name($name, $except)
            ->email($email, $except)
            ->type($type)
            ->business($business)
            ->paginate($page);

        return ContactResourceList::collection($contacts);
    }

    /**
     * Almacena un nuevo contacto
     * @param  $request a traves del body json
     * @return Json api rest
     */
    public function store(ContactRequest $request)
    {
        try {
            if (!count($request->all())) {
                return response()->json([
                    'message' => 'No se proporcionaron datos',
                ], 400);
            }

            $request['telnumbers'] = $request->phones;

            if ($request->has('birthday') && $request->birthday) {
                $request['birthday'] = Carbon::parse($request->birthday)->toDateString();
            }
            if (!$request->has('business')) {
                $request['business'] = false;
            }

            $contact = $this->contact->create($request->all());
            $contact->saveMetas($request);
            $contact->load([
                'user',
                'user_updated',
                'buys.pedido',
                'orders.nota',
                'supplier.nota',
                'exams',
                'brands',
                'metas',
                'phones'
            ]);
            Log::info("[contact.store] Save user successfully: " . $contact->id);
            return new ContactResource($contact);
        } catch (\Throwable $th) {
            Log::error("[contact.store] Error saving user: " . $th->getMessage(), [
                'user' => $request->all(),
                'line' => $th->getLine(),
                'file' => $th->getFile()
            ]);
            return response()->json([
                'message' => 'Error al guardar el usuario',
                'error' => $th->getMessage()
            ], 500);
        }
    }

    /**
     * Muestra un contacto en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show(Request $request, $id)
    {
        $version = $request->input('version', 'v1');
        $perPage = 10;

        $contact = $this->contact::findOrFail($id);

        if ($version == 'v1') {
            $contact->load(['phones']);
            $contact->loadCount(['buys', 'brands', 'exams', 'supplier', 'orders', 'phones']);

            // Manual pagination for Resource
            $contact->setRelation('exams', $contact->exams()->with('user')->paginate($perPage, ['*'], 'exam_page'));
            $contact->setRelation('supplier', $contact->supplier()->paginate($perPage, ['*'], 'suppliers_page'));
            $contact->setRelation('brands', $contact->brands()->paginate($perPage, ['*'], 'brands_page'));
            $contact->setRelation('buys', $contact->buys()->paginate($perPage, ['*'], 'purchases_page'));
            $contact->setRelation('orders', $contact->orders()->paginate($perPage, ['*'], 'orders_page'));

            // Filtered metas
            $contact->setRelation('metas', $contact->metas()
                ->whereIn('key', ['metadata', 'updated', 'deleted', 'created'])
                ->orderBy('id', 'desc')
                ->get());

            return new ContactResource($contact);
        }

        // Version v2 (lightweight)
        $contact->load(['phones', 'exams', 'metas', 'orders', 'user', 'user_updated']);
        return new ContactShowV2($contact);
    }

    /**
     * Actualiza un contacto en espesifico
     * @param  $request body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(ContactRequest $request, Contact $contact)
    {
        $request['user_id'] = $contact->user_id;
        if ($request->has('phones')) {
            $request['telnumbers'] = $request->phones;
        }

        if ($request->has('birthday') && $request->birthday) {
            $request['birthday'] = Carbon::parse($request->birthday)->toDateString();
        }
        Log::info("[contact.update] Update contact: " . $contact->id, $request->all());
        $contact->update($request->all());
        $contact->saveMetas($request);
        return new ContactResource($contact);
    }

    /**
     * Elimina un contacto en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy($id)
    {
        $contact = $this->contact::where('id', $id)
            ->withRelation()
            ->withUsageCounts()
            ->first();

        $enUso = $contact->en_uso;

        if ($enUso) {
            $contact->save();
            $contact->delete(); // Soft delete
        } else {
            $contact->forceDelete(); // Hard delete
        }

        Log::info("[contact.destroy] Delete contact: " . $contact->id);
        return response()->json(null, 204);
    }

    /**
     * Retorna estadisticas del contacto
     * @param  int  $id
     * @return Json api rest
     */
    public function stats($id)
    {
        $contact = $this->contact::where('id', $id)
            ->withUsageCounts()
            ->firstOrFail();

        if ($contact->type == 0) {
            // Patient
            $data = [
                'buys' => $contact->buys_count ?? 0,
                'orders' => $contact->orders_count ?? 0,
                'exams' => $contact->exams_count ?? 0,
                'phones' => $contact->phones_count ?? 0,
            ];
        } else {
            // Supplier
            $data = [
                'supplier' => $contact->supplier_count ?? 0,
                'brands' => $contact->brands_count ?? 0,
                'phones' => $contact->phones_count ?? 0,
            ];
        }

        return response()->json($data);
    }
}
