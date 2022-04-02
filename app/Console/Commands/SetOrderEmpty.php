<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use Carbon\Carbon;

class SetOrderEmpty extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:setordersempty {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Elimina ordenes no procesadas en pedidos';

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
        try {
            $dateInto = $this->argument('date');
            return;
            if (!$dateInto) {
                $dateInto = date('d/m/Y');
            }

            $date = Carbon::createFromFormat('d/m/Y', $dateInto);
            $orders = Order::WhereDate("created_at", "<=", $date)
                ->where('status', 0)
                ->whereNull('deleted_at')
                ->get();

            if (!count($orders)) {
                $this->info('No hay pedidos para procesar antes de esta fecha: ' . $date->format('d M Y'));
                return 0;
            }

            if (!$this->confirm('Se eliminaran (' . count($orders) . ') pedidos antes de la fecha: ' . $date->format('d M Y') . ' :::: ¿Desea continuar?', true)) {
                $this->info('Operacion cancelada!');
                return 0;
            }
            //$this->newLine();

            if ($orders) {
                $bar = $this->output->createProgressBar(count($orders));
                $bar->start();
                foreach ($orders as $order) {
                    $order->deleted_at = Carbon::now();
                    $order->updated_id = 1;
                    $order->save();

                    $bar->advance();
                }

                $bar->finish();
            }
            //$this->newLine();
            $this->info(' ::: Proceso terminado con exito: ');

            return false;
        } catch (\Throwable $th) {
            $this->error('[ERROR] ' . $th->getMessage());
            //report($th);
            return false;
        }
    }
}
