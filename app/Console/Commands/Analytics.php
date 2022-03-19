<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SaleItem;
use Carbon\Carbon;

class Analytics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:analytics {year}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'To make csv files about analytics';

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
        $year = $this->argument('year');

        if (!$year) {
            $this->info('The param year is required');
            return 0;
        }
        $today = new Carbon("America/Mexico_City");
        if (!is_numeric($year) || $year > $today->format("Y")) {
            $this->info("$year is no valid year.");
            return 0;
        }

        $this->analytic_sales($year);

        return 0;
    }

    public function analytic_sales($year)
    {
        $date_search = new Carbon($year . "-01-01", "America/Mexico_City");
        $sales = SaleItem::whereYear("created_at", $date_search->format("Y"))->with("item", "branch")->get();
        $filename = storage_path('app/analytics_sales_' . $year . '.csv');
        $columns = array(
            'session',
            'code',
            'product',
            'category',
            'cant',
            'price',
            'branch',
            "customer",
            "age",
            "gender",
            'employ',
            'date',
            'month',
        );

        $file = fopen($filename, 'w');
        fputcsv($file, $columns);

        $bar = $this->output->createProgressBar($sales->count());
        $bar->start();
        $no_count = [];

        foreach ($sales as $st) {
            $branch = "undefined";
            $main_category = "undefined";
            $age = "undefined";
            $gender = "undefined";

            if ($st->saleDetails && $st->saleDetails->cliente) {
                if ($st->saleDetails->cliente->metas->count()) {
                    $metas = $st->saleDetails->cliente->metas[0];

                    if ($metas->key === "metadata" && $metas->value) {
                        $metas = $metas->value;

                        if ($metas["gender"]) {
                            $gender = $metas["gender"];
                        }

                        if ($metas["birthday"] && $metas["birthday"] != "0000-00-00") {

                            $birthDay = new Carbon($metas["birthday"], "America/Mexico_City");
                            $age = $birthDay->diffInYears(carbon::now());
                        }
                    }
                }
            } else {
                $no_count[] = $st->session;
                continue;
            }

            if ($st->branch->value) {
                $valueJson = json_decode($st->branch->value, true);
                $branch = $valueJson["name"];
            }

            if ($st->item->categoria) {
                $main_category = $st->item->categoria->getMainCategory();
                if ($main_category) $main_category = $main_category->name;
            }

            fputcsv($file, array(
                (string) $st->session,
                (string) $st->item->code,
                (string) $st->item->name,
                (string) $main_category,
                (int) $st->cant,
                (int) $st->price,
                (string) $branch,
                (string) $st->saleDetails->cliente->name,
                (int) $age,
                (string) $gender,
                (string) $st->user->name,
                (string) $st->created_at->format("Y-m-d"),
                (int) $st->created_at->format("m"),
            ));
            $bar->advance();
        }
        $bar->finish();
        fclose($file);
        $this->info("\n Create file successfully: " . $filename);

        if (count($no_count)) {
            $this->info("But, we have some problems with sales lost");
            foreach ($no_count as $value) {
                $this->info("Session: " . $value);
            }
        }

        return true;
    }
}
