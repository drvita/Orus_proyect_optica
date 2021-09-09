<?php

namespace App\Http\Controllers;

use App\Http\Resources\Config as ResourcesConfig;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    protected $config;

    public function __construct(Config $config){
        $this->config = $config;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby? $request->orderby : "value";
        $order = $request->order=="desc"? "desc" : "asc";
        $page = $request->itemsPage ? $request->itemsPage : 50;
        
        $config = $this->config
            ->orderBy($orderby, $order)
            ->name($request->name)
            ->paginate($page);
        return ResourcesConfig::collection($config);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(is_string($request->value)){
            $value = $request->value;
        } else {
            $value = json_encode($request->value);
        }

        $category = Config::create([
            'name' => $request->input('name'),
            'value' => $value,
        ]);
        return New ResourcesConfig($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function show(Config $config)
    {
        return New ResourcesConfig($config);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Config $config)
    {
        $config->update( $request->all() );
        return New ResourcesConfig($config);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function destroy(Config $config)
    {
        $config->delete();
        return response()->json(null, 204);
    }
}
