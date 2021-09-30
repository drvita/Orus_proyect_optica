<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreItem;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class GetStoreCvs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:getstorecsv {branch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea archivos CSV de productos en almacen';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id_branch = (int) $this->argument('branch');
        if (!$this->confirm('Desea continuar con la generacion del archivo CVS?', true)) {
            $this->info('\n Operacion cancelada!');
            return 0;
        }

        $date = Carbon::now();
        $filename = storage_path('/app/store_'. $date->format('d_m_Y-hms') .'.csv');
        $store = StoreItem::with('categoria')->orderBy('name')->get();
        /*
        $store_lot = StoreItem::with('lote')->whereHas('lote', function(Builder $query) use ($id_branch){
            $query->where('branch_id', '==', $id_branch);
        })->get();
        */

        $columns = array(
            'id', 
            'codigo', 
            'codigo_barra', 
            'graduacion',
            'nombre',
            'categoria',
            'unidad',
            'cantidad', 
            'precio',
            'almacen',
        );
        
        $file = fopen($filename, 'w');
        fputcsv($file, $columns);
        $bar = $this->output->createProgressBar(count($store));
        $bar->start();
        
        foreach($store as $st) {
            fputcsv($file, array(
                (int) $st->id,
                (string) $st->code,
                (string) $st->codebar,
                (string) $st->grad,
                (string) $st->name,
                (string) $st->categoria->name,
                (string) $st->unit,
                (int) $st->cant, 
                (float) $st->price,
                (string) 'sin definir',
            ));
            $bar->advance();
        }
        $bar->finish();
        fclose($file);
        $this->info('\n Archivo creado con exito: '. $filename);
    }
}