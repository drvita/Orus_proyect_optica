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
        //turn into array
        $file = file($path);
        //remove first line
        $data = array_slice($file, 1);
        $data = array_map('str_getcsv', $data);
        //$bar = $this->output->createProgressBar(count($store));
        //$bar->start();

        //loop over the data
        foreach ($data as $row) {
            $store = StoreItem::find((int) $row[0]);

            if ($store) {
                if ($row[1]) $store->code = (string) $row[1];
                if ($row[2]) $store->codebar = (string) $row[2];
                if ($row[3]) $store->grad = (string) $row[3];
                if ($row[4]) $store->name = (string) $row[4];
                if ($row[5]) $store->unit = (string)$row[5];
                if (!empty($row[6])) $store->cant = (int) $row[6];
                if (!empty($row[7])) $store->price = (float) $row[7];
                $store->save();
                $this->info('Actualizando registro para: ' . (string) strtoupper($row[4]) . " (" . (int) $row[6] . ")");
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
        $this->newLine();
        $this->info('::: ALMACEN ACTUALIZADO CON EXITO :::');
    }
}