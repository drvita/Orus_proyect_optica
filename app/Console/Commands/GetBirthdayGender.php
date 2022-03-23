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
        $contacts = Contact::where("type", 0)->with("metas")->skip($start)->take(1000)->orderBy("created_at", "DESC")->get();
        $count = 1;

        foreach ($contacts as $contact) {
            $name = explode(" ", $contact->name);
            $response = Http::get("https://api.genderize.io/?name=" . urlencode($name[0]) . "&country_id=MX");
            $body = $response->status() >= 200 && $response->status() < 300 ? $response->json($key = null) : null;
            $metadata = $contact->metas()->where("key", "metadata")->first();

            $data = [
                "gender" => $metadata ? $metadata->value["gender"] : "",
                "birthday" => $metadata ? $metadata->value["birthday"] : "0000-00-00",
            ];

            if ($contact->birthday) {
                $birthday = $contact->birthday->format("Y-m-d");
                if ($birthday != "-0001-11-30 00:00:00") {
                    $data["birthday"] = $contact->birthday->format("Y-m-d");
                }
            }

            if ($body) {
                $data["gender"] = $body["gender"];
            }

            if ($metadata) {
                $metadata->value = $data;
                $metadata->save();
            } else {
                $contact->metas()->create(["key" => "metadata", "value" => $data]);
            }

            $contact->name = strtolower($contact->name);
            $contact->birthday = $data["birthday"];
            $contact->save();
            $this->info('[Orus] (' . $count . ') - working with: ' . $contact->name . " - " . $data["gender"]);
            $count++;
        }

        return 0;
    }
}
