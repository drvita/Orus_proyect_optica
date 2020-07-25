<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Contact as ContactResource;
use App\Http\Requests\Contact as ContactRequests;
use App\Models\Contact;

class ContactController extends Controller{
    protected $contact;

    public function __construct(Contact $contact){
        $this->contact = $contact;
    }
    /**
     * Muestra la lista de contactos
     * @return Json api rest
     */
    public function index(){
        return ContactResource::collection(
            $this->contact::all()
        );
    }

    /**
     * Almacena un nuevo contacto
     * @param  $request a traves del body json
     * @return Json api rest
     */
    public function store(ContactRequests $request){
        $request['user_id']=Auth::id();
        $contact = $this->contact->create($request->all());
        return New ContactResource($contact);
    }

    /**
     * Muestra un contacto en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show(Contact $contact){
        return New ContactResource($contact);
    }

    /**
     * Actualiza un contacto en espesifico
     * @param  $request body json
     * @param  int  $id
     * @return Json api rest
     */
    public function update(ContactRequests $request, Contact $contact){
        $request['user_id']=$contact->user_id;
        $contact->update( $request->all() );
        return New ContactResource($contact);
    }

    /**
     * Elimina un contacto en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(Contact $contact){
        $contact->delete();
        return response()->json(null, 204);
    }
}
