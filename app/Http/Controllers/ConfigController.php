<?php

namespace App\Http\Controllers;

use App\Http\Resources\Config as ResourcesConfig;
use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->middleware('can:config.list')->only('index');
        $this->middleware('can:config.show')->only('show');
        $this->middleware('can:config.add')->only('store');
        $this->middleware('can:config.edit')->only('update');
        $this->middleware('can:config.delete')->only('destroy');
        $this->config = $config;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $orderby = $request->orderby ? $request->orderby : "value";
        $order = $request->order == "desc" ? "desc" : "asc";
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
        if (is_string($request->value)) {
            $value = $request->value;
        } else {
            $value = json_encode($request->value);
        }

        $category = Config::create([
            'name' => $request->input('name'),
            'value' => $value,
        ]);
        return new ResourcesConfig($category);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Config  $config
     * @return \Illuminate\Http\Response
     */
    public function show(Config $config)
    {
        return new ResourcesConfig($config);
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
        $data = $request->all();

        if (!is_string($data['value'])) {
            $data['value'] = json_encode($data['value']);
        }

        $config->update($data);
        return new ResourcesConfig($config);
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
