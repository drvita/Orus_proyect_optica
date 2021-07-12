<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Contact as ContactResource;
use App\Http\Resources\ContactList as ContactResourceList;
use App\Http\Requests\Contact as ContactRequests;
use App\Models\Contact;
use Carbon\Carbon;

class ContactController extends Controller{
    protected $contact;

    public function __construct(Contact $contact){
        $this->contact = $contact;
    }
    /**
     * Muestra la lista de contactos
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "created_at";
        $order = $request->order=="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $contacts = $this->contact
                ->withRelation()
                ->orderBy($orderby, $order)
                ->searchUser($request->search, $request->except)
                ->name($request->name)
                ->email($request->email)
                ->type($request->type)
                ->business($request->business)
                ->publish()
                ->paginate($page);
            //dd($contacts->toArray());
        return ContactResourceList::collection($contacts);
    }

    /**
     * Almacena un nuevo contacto
     * @param  $request a traves del body json
     * @return Json api rest
     */
    public function store(ContactRequests $request){
        $validated = $request->validate([
            'name' => 'required|unique:contacts',
            'email' => 'unique:contacts',
        ]);

        $request['user_id']= Auth::user()->id;
        $contact = $this->contact->create($request->all());
        return New ContactResource($contact);
    }

    /**
     * Muestra un contacto en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show($id){
        $contact = $this->contact::where('contacts.id',$id)
                    ->withRelation()
                    ->first();

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
        $request['updated_id'] = Auth::user()->id;
        $contact->update( $request->all() );
        return New ContactResource($contact);
    }

    /**
     * Elimina un contacto en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy($id){
        $contact = $this->contact::where('id', $id)
                    ->withRelation()
                    ->first();

        $enUso = count($contact->buys) + count($contact->orders) + count($contact->supplier) + count($contact->exams) + count($contact->brands);

        if($enUso){
            $contact->deleted_at = Carbon::now();
            $contact->updated_id = Auth::user()->id;
            $contact->save();
        } else {
            $contact->delete();
        }

        return response()->json(null, 204);
    }
}
