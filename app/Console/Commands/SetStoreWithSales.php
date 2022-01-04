<?php

namespace App\Console\Commands;

use App\Models\SaleItem;
use App\Models\StoreBranch;
use App\Models\StoreItem;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SetStoreWithSales extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:setstoresales {start} {end?} {--catid=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'down items of store whit sales in determinate dates';

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
        $start = null;
        $end = null;
        $catidCheck = (int) $this->option('catid');

        if (!$this->argument('start')) {
            $this->info('Es necesario establecer fecha de inicio!');
            return 0;
        }
        $start = date($this->argument('start'));
        if (!$this->argument('end')) {
            $end = date("Y-m-d");
        } else {
            $end = date($this->argument('end'));
        }

        if (!$this->confirm('Desea continuar con la ejecución de este comando?')) {
            $this->info('Operacion cancelada!');
            return 0;
        }

        $this->info('Iniciando operaciones: ' . $start . ' - ' . $end);
        $sales = SaleItem::whereBetween('created_at', [$start, $end])->get();
        $total = count($sales);
        $date = Carbon::now();
        $filename = storage_path('/app/store_uptade_sales' . $date->format('d_m_Y-hms') . '.csv');
        $made = [];
        $this->info('Total de registros a dar de baja: ' . $total);
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($sales as $sale) {
            $itemData = StoreItem::where('id', $sale->store_items_id)
                ->publish()
                ->with(['categoria.parent.parent.parent'])
                ->first();

            if ($itemData) {
                $catid = $itemData->category_id;
                if ($itemData->categoria->parent) {
                    $catid = $itemData->categoria->parent->id;
                    if ($itemData->categoria->parent->parent) {
                        $catid = $itemData->categoria->parent->parent->id;
                        if ($itemData->categoria->parent->parent->parent) {
                            $catid = $itemData->categoria->parent->parent->parent->id;
                        }
                    }
                }

                if ($catidCheck && $catidCheck === $catid) continue;

                $made[] = $sale->processInStorageItem($sale, "created");
            } else {
                $made[] = [
                    "itemId" => $sale->store_item_id,
                    "name" => "",
                    "code" => "",
                    "branch" => $sale->branch_id,
                    "cant" => $sale->cant,
                    "status" => "failer",
                    "message" => "Item not found in store"
                ];
            }
            $bar->advance();
        }
        $bar->finish();

        $columns = array(
            'SaleId',
            'itemId',
            'name',
            'code',
            'branch',
            'cant',
            'status',
            'message'
        );
        $file = fopen($filename, 'w');
        fputcsv($file, $columns);
        foreach ($made as $st) {
            if (!isset($st["itemId"])) continue;

            fputcsv($file, array(
                (int) $st["saleID"],
                (int) $st["itemId"] ? $st["itemId"] : 0,
                (string) $st["name"],
                (string) $st["code"],
                (string) $st["branch"],
                (int) $st["cant"],
                (string) $st["status"],
                (string) $st["message"]
            ));
        }
        fclose($file);
        // $this->info('Operaciones concluidas exitosamente: ' . count($made) . '/' . $total);
        return 1;
    }
}