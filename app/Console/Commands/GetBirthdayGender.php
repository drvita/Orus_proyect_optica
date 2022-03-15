<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Contact;

class GetBirthdayGender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:changeMeta {start}';

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
        $start = (int) $this->argument('start');

        $this->info('Start with change metadata init: ' . $this->argument('start'));
        $contacts = Contact::where("type", 0)->with("metas")->skip($start)->take(1000)->get();

        foreach ($contacts as $contact) {
            // $this->info('[Orus] working with:'. $contact->name);
            $name = explode(" ", $contact->name);
            $birthday = $contact->birthday->format("Y-m-d");
            $response = Http::get("https://api.genderize.io/?name=" . urlencode($name[0]) . "&country_id=MX");
            $body = $response->status() >= 200 && $response->status() < 300 ? $response->json($key = null) : null;
            $metadata = $contact->metas()->where("key", "metadata")->first();
            $data = [
                "gender" => "",
                "birthday" => $birthday !== "-0001-11-30" ? $birthday : "0000-00-00",
            ];

            if ($body) {
                $data["gender"] = $body["gender"];
            }
            $this->info('[Orus] working with: ' . $contact->name . " - " . $data["gender"]);

            if ($metadata) {
                $metadata->value = $data;
                $metadata->save();
            } else {
                $contact->metas()->create(["key" => "metadata", "value" => $data]);
            }
        }

        return 0;
    }
}