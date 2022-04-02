<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\StoreItem;
use App\Models\StoreBranch;
use PhpParser\Node\Expr\Cast\Bool_;

class SetStoreCvs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:setstorecsv {file} {--branch=} {--nocheck}';

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

    public function saveStoreItem($row, $branchId)
    {
        // Save general data
        $toSave = [
            'id' => (int) $row['id'],
            'code' => (string) $row['codigo'],
            'codebar' => $row['codigo_barra'] ? (string) $row['codigo_barra'] : null,
            'grad' => (string) $row['graduacion'],
            'name' => (string) $row['nombre'],
            'unit' => (string)$row['unidad'],
            'branch_default' => $row['almacen'] ? (int)$row['almacen'] : null,
            'updated_id' => 1,
        ];
        $storeItem = StoreItem::updateOrCreate([
            'id' => $toSave['id'],
        ], $toSave);

        if ($storeItem && $storeItem->id) {
            // Save branch data
            $cant = (int) $row['cantidad'] > 0 ? (int) $row['cantidad'] : 0;
            $toBranches = [
                'store_item_id' => $storeItem->id,
                "cant" => $cant,
                "price" => (float) $row['precio'],
                "branch_id" => (int) $branchId,
                'updated_id' => 1,
                'user_id' => 1,
            ];
            StoreBranch::updateOrCreate([
                'store_item_id' => $toBranches['store_item_id'],
                'branch_id' => $toBranches['branch_id'],
            ], $toBranches);
        }
    }
    public function getCanInBranches($inBranches, $branchId)
    {
        if (!$inBranches) {
            return 0;
        }
        $cant = 0;

        foreach ($inBranches as $branch) {
            if ($branch["branch_id"] === $branchId) {
                $cant = $branch["cant"];
                break;
            }
        }

        return $cant;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $path = $this->argument('file');
        $branch = (int) $this->option('branch');
        $nocheck = $this->option('nocheck');
        return;

        if (!$path) {
            $this->info('Operacion cancelada por NO introduccion el archivo CSV!');
            return 0;
        }
        if (!$branch) {
            $this->info('Operacion cancelada, es necesesario espesificar el ID del almacen. EJ: --branch=12');
            return 0;
        }
        if (!$this->confirm('Desea continuar con la importacion del archivo CVS?')) {
            $this->info('Operacion cancelada!');
            return 0;
        }

        $file = file($path);
        $csv = array_map('str_getcsv', $file);

        array_walk($csv, function (&$a) use ($csv) {
            $a = array_combine($csv[0], $a);
        });
        array_shift($csv); # remove column header

        $bar = $this->output->createProgressBar(count($csv));
        $bar->start();

        foreach ($csv as $row) {
            $store = StoreItem::where("id", (int) $row['id'])
                ->with('inBranch')
                ->publish()
                ->first();

            if ($store) {
                // Updated item
                $cant_csv = (int) $row['cantidad'];
                $cant_store = $this->getCanInBranches($store->inBranch->toArray(), $branch); //$store->cant;

                if ($cant_store !== $cant_csv || $nocheck) {
                    $this->saveStoreItem($row, $branch);
                }
            } else {
                // new Item
                $this->saveStoreItem($row, $branch);
            }
            $bar->advance();
        }
        $bar->finish();

        $this->info("\n" . '::: ALMACEN ACTUALIZADO CON EXITO :::');
    }
}
