<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Contact as ContactResource;
use App\Http\Resources\ContactList as ContactResourceList;
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
        $orderby = $request->orderby ? $request->orderby : "created_at";
        $order = $request->order == "desc" ? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;
        $contacts = $this->contact
            ->withRelation()
            ->orderBy($orderby, $order)
            ->searchUser($request->search)
            ->name($request->name, $request->except)
            ->email($request->email, $request->except)
            ->type($request->type)
            ->business($request->business)
            ->publish()
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

            $request['user_id'] = Auth::user()->id;
            $request['telnumbers'] = $request->phones;

            if ($request->has('birthday') && $request->birthday) {
                $request['birthday'] = Carbon::parse($request->birthday)->toDateString();
            }

            $contact = $this->contact->create($request->all());
            $contact->saveMetas($request);
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
    public function show($id)
    {
        $contact = $this->contact::where('contacts.id', $id)
            ->withRelation()
            ->first();

        return new ContactResource($contact);
    }

    /**
     * Actualiza un contacto en espesifico
     * @param  $request body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(ContactRequest $request, Contact $contact)
    {
        $currentUser = Auth::user();
        $request['user_id'] = $contact->user_id;
        $request['updated_id'] = $currentUser->id;
        $request['telnumbers'] = $request->phones;

        if ($request->has('birthday') && $request->birthday) {
            $request['birthday'] = Carbon::parse($request->birthday)->toDateString();
        }

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
            ->first();

        $enUso = count($contact->buys) + count($contact->orders) + count($contact->supplier) + count($contact->exams) + count($contact->brands);

        if ($enUso) {
            $contact->deleted_at = Carbon::now();
            $contact->updated_id = Auth::user()->id;
            $contact->save();
        } else {
            $contact->delete();
        }

        return response()->json(null, 204);
    }
}
