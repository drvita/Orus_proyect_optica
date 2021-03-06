<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Category as CategoryResource;
//use App\Http\Resources\CategoryInExam as CategoryResource;
use App\Models\Category;

class CategoryController extends Controller
{
    protected $category;
    
    public function __construct(Category $category){
        $this->category = $category;
    }
    /**
     * Muestra una lista de categorias
     * @return Json api rest
     */
    public function index(Request $request){
        $orderby = $request->orderby? $request->orderby : "category_id";
        $order = $request->order=="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;

        $categories = $this->category
                ->withRelation()
                ->orderBy($orderby, $order)
                ->categoryId($request->categoryid)
                ->search($request->search)
                ->paginate($page);

        //return $categories;
        return CategoryResource::collection($categories);
    }

    /**
     * Crea categorias nuevas
     * @param  $request de body json
     * @return json api rest
     */
    public function store(Request $request){
        $category = Category::create([
            'name' => $request->input('name'),
            'descripcion' => $request->input('descripcion'),
            'category_id' => $request->input('category_id'),
            'user_id' => Auth::user()->id
        ]);
        return New CategoryResource($category);
    }

    /**
     * Muestra una categoria en espesifico
     * @param  int  $id
     * @return Json api rest
     */
    public function show($id){
        $category = $this->category::where('id', $id)
                    ->withRelation()
                    ->first();

        return new CategoryResource($category);
    }

    /**
     * Actualiza una categoria espesifica
     * @param  $request de body json
     * @param  int  $id
     * @return Json Api rest
     */
    public function update(Request $request, Category $category){
        $fill = array();
        if($request->input('name')) $fill['name'] = $request->input('name');
        if($request->input('descripcion')) $fill['descripcion'] = $request->input('descripcion');
        if($request->input('category_id')) $fill['category_id'] = $request->input('category_id');
        if($fill) $category->fill($fill)->save();
        return New CategoryResource($category);
    }

    /**
     * Elimina una categoria espesifica
     * @param  int  $id
     * @return Json api rest
     */
    public function destroy(Category $category){
        $category->delete();
        return response()->json(null, 204);
    }
}
