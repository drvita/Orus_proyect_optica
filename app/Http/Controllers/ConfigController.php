<?php

namespace App\Http\Controllers;

use App\Http\Resources\Config as ResourcesConfig;
use App\Http\Resources\BranchesStore;
use App\Http\Resources\BankStore;
use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ConfigController extends Controller
{
    protected $config;

    public function __construct(Config $config)
    {
        $this->middleware('can:config.list')->only('index');
        $this->middleware('can:config.show')->only('show');
        // $this->middleware('can:config.add')->only('store');
        // $this->middleware('can:config.edit')->only('update');
        // $this->middleware('can:config.delete')->only('destroy');
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
        $query = $this->config
            ->orderBy($orderby, $order)
            ->name($request->name);

        if ($request->has('itemsPage') && ($request->itemsPage === 0 || $request->itemsPage === '0')) {
            $limit = $request->input('limit', 100);
            $config = $query->limit($limit)->get();
        } else {
            $page = $request->input('itemsPage', 50);
            $config = $query->paginate($page);
        }

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
        // Log::debug("[ConfigController] Store request: ", $request->all());
        if (is_string($request->value)) {
            $value = $request->value;
        } else {
            $value = json_encode($request->value);
        }

        $config = Config::create([
            'name' => $request->input('name'),
            'value' => $value,
        ]);
        Log::info("[ConfigController] Store: " . $config->name);
        return new ResourcesConfig($config);
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
        Log::info("[ConfigController] Update: " . $config->name);
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
        return response()->json($config, 204);
    }

    /**
     * Devuelve solo las sucursales
     */
    public function branches()
    {
        $branches = $this->config->where('name', 'branches')->get();
        return BranchesStore::collection($branches);
    }

    /**
     * Devuelve solo los bancos
     */
    public function banks()
    {
        $banks = $this->config
            ->where('name', 'bank')
            ->orderBy('value', 'asc')
            ->get();
        return BankStore::collection($banks);
    }

    /**
     * Devuelve solo los partners
     */
    public function partners()
    {
        $partners = $this->config
            ->where('name', 'partner')
            ->orderBy('value', 'asc')
            ->get();
        return BankStore::collection($partners);
    }
}
