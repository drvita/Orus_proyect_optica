<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreItem;
use Carbon\Carbon;

class GetStoreCvs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:getstorecsv {--Z|zero: mostrar solo productos en cero}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crea archivos CVS de productos en almacen';

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
        //$zero = (boolean) $this->argument('zero');
        if (!$this->confirm('Desea continuar con la generacion del archivo CSV?', true)) {
            $this->info('Operacion cancelada!');
            return 0;
        }

        $date = Carbon::now();
        $filename = storage_path('app/store_' . $date->format('d_m_Y-hms') . '.csv');
        $store = StoreItem::All()->sortBy('name');
        $columns = array(
            'id',
            'codigo',
            'codigo_barra',
            'graduacion',
            'nombre',
            'unidad',
            'cantidad',
            'precio',
            //'marca',
            //'categoria',
        );

        $file = fopen($filename, 'w');
        fputcsv($file, $columns);
        $bar = $this->output->createProgressBar(count($store));
        $bar->start();

        foreach ($store as $st) {
            fputcsv($file, array(
                (int) $st->id,
                (string) $st->code,
                (string) $st->codebar,
                (string) $st->grad,
                (string) $st->name,
                (string) $st->unit,
                (int) $st->cant,
                (float) $st->price,
                //(string) $st->brand ? $st->brand->name : '',
                //(int) $st->category_id,
            ));
            $bar->advance();
        }
        $bar->finish();
        fclose($file);
        $this->info("\n" . 'Archivo creado con exito: ' . $filename);
    }
}