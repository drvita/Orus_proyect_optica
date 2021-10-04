<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreItem;

class SetStoreCvs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:setstorecsv {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Importa archivos CSV a productos en almacen';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function saveStoreItem($row, $store){

        if($row['codigo']) $store->code = (string) $row['codigo'];
        if($row['codigo_barra']) $store->codebar = (string) $row['codigo_barra'];
        if($row['graduacion']) $store->grad = (string) $row['graduacion'];
        if($row['nombre']) $store->name = (string) $row['nombre'];
        if($row['unidad']) $store->unit = (string)$row['unidad'];
        if(!empty($row['cantidad'])) $store->cant = (string)$row['cantidad'];
        if(!empty($row['precio'])) $store->price = (float) $row['precio'];

        $store->save();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('file');
        if (!$path) {
            $this->info('Operacion cancelada por NO introduccion el archivo CSV!');
            return 0;
        }
        if (!$this->confirm('Desea continuar con la importacion del archivo CVS?')) {
            $this->info('Operacion cancelada!');
            return 0;
        }
        
        $file = file($path);
        //remove first line
        //$data = array_slice($file, 1);
        $csv = array_map('str_getcsv', $file);

        array_walk($csv, function(&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv); # remove column header
        
        

        foreach($csv as $row) {
            $store = StoreItem::where("id",(int) $row['id'])
                        ->with('lote')
                        ->publish()
                        ->first();

            if($store){
                $cant_csv = (int)$row['cantidad'];
                $cant_store = $store->cant;
                
                if(count($store->lote)){
                    $cant_store = 0;
                    foreach ($store->lote as $lot) {
                        if($lot->branch_id === $row['almacen']) $cant_store += $lot->amount;
                    }
                }

                dd("Lote", $store->lote);
                //I'M HERE

                if($cant_store === $cant_csv ){
                    dd("Iguales", $store->cant, $cant_csv);

                    $this->saveStoreItem($row, $store);
                } else {
                    if($cant_csv > $store->cant){
                        dd("CSV mayor", $store->cant, $cant_csv);

                        

                        
                    } else {
                        dd("CSV menor", $store->cant, $row);

                        if($row['codigo']) $store->code = (string) $row['codigo'];
                        if($row['codigo_barra']) $store->codebar = (string) $row['codigo_barra'];
                        if($row['graduacion']) $store->grad = (string) $row['graduacion'];
                        if($row['nombre']) $store->name = (string) $row['nombre'];
                        if($row['nombre']) $store->unit = (string)$row['unidad'];
                        if(!empty($row['nombre'])) $store->cant = $cant_csv;
                        if(!empty($row['precio'])) $store->price = (float) $row['precio'];

                        $store->save();
                    }
                }
                
                //$this->info('Actualizando registro para: '. (string) strtoupper($row[4]) ." (". (int) $row[6] .")" );
            } else {
                //Producto nuevo
            }

            /*
            StoreItem::updateOrCreate([
                'id' => (int) $row[0],
            ], [
                'code' => (string) $row[1],
                'codebar' => (string) $row[2],
                'grad' => (string) $row[3],
                'name' => (string) $row[4],
                'unit' => (string)$row[5],
                'cant' => (int) $row[6],
                'price' => (float) $row[7],
                //'category_id' => (int) $row[8],
            ]);
            //$bar->advance();
            */
            
        }
        //$bar->finish();
        
        $this->info('::: ALMACEN ACTUALIZADO CON EXITO :::');
        
    }
}