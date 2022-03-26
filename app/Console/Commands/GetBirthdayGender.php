<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Contact;
use Carbon\Carbon;

class GetBirthdayGender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:changeMeta {wait}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'change values to metadata (birthday and gender)';

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
        $wait = (int) $this->argument('wait');
        $contacts = Contact::where("type", 0)->with("metas")->get();
        $this->info('Start change metadata with: ' . $contacts->count() . ' contacts to do');
        $count = 0;
        $countNull = 0;
        $madeHttp = 0;
        $badwords = ["venta", "banco", "colegio", "cementos", "consejo", "icono", "instituto", "maquinas", "mariscos", "municipio", "ofitec", "procesadora", "suprema", "sistema", "servicios", "", " "];
        $newBadWords = [];
        $okWords = [];

        foreach ($contacts as $contact) {
            $count++;
            $metadata = $contact->metas()->where("key", "metadata")->first();
            if ($metadata && $metadata->value && isset($metadata->value['gender'])) {
                continue;
            }
            $data = [
                "gender" => "",
                "birthday" => "",
            ];
            $body = null;
            $nameArray = preg_split('/[\s\.]/i', $contact->name, 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
            $name = strtolower(normaliza($nameArray[0]));
            if (in_array($name, $badwords)) {
                continue;
            }

            if (count($okWords)) {
                if (array_key_exists($name, $okWords)) {
                    $body = $okWords[$name];
                }
            }

            if (!$body) {
                $response = Http::get("https://api.genderize.io/?name=" . urlencode($name) . "&country_id=MX");
                $body = $response->status() >= 200 && $response->status() < 300 ? $response->json($key = null) : null;
                $madeHttp++;
            }

            if ($contact->birthday) {
                $data["birthday"] = $contact->birthday->format("Y-m-d");
            }

            if (!$body || !$body["gender"]) {
                $countNull++;
                if ($countNull > $wait) break;
                $badwords[] = $name;
                $newBadWords[] = $name;
                $this->info('[Orus] (' . $count . ') Not work with: ' . $name . " / " . $countNull);
                continue;
            }
            $data["gender"] = $body["gender"];
            $okWords[$name] = $body;

            if ($metadata) {
                if ($metadata->value["birthday"] && $metadata->value["birthday"] != "0000-00-00") {
                    $data["birthday"] =  $metadata->value["birthday"];
                }
                $metadata->value = $data;
                $metadata->save();
            } else {
                $contact->metas()->create(["key" => "metadata", "value" => $data]);
            }

            if ($data["birthday"] !== "0000-00-00" && $data["birthday"] !== "") {
                $contact->birthday = $data["birthday"];
            }
            $contact->name = strtolower(normaliza($contact->name));

            $contact->save();
            $this->info('[Orus] (' . $count . ') - made: ' . $contact->name . " - " . $data["gender"]);
            $countNull = 0;

            if ($madeHttp > 1000) break;
        }

        if (count($newBadWords)) {
            $this->info("[Orus] We have new bad words");
            $filename = storage_path('app/names_notwork.csv');
            $file = fopen($filename, 'a');
            foreach ($newBadWords as $row) {
                fputcsv($file, [$row]);
            }
        }
        $this->info("[Orus] ::: Job finish :::");

        return 0;
    }
}
