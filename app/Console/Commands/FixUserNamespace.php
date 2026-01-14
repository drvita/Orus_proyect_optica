<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FixUserNamespace extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:fix-user-namespace';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Updates polymorphic types in database from App\User to App\Models\User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting namespace repair...');

        // 1. model_has_roles
        $rolesCount = DB::table('model_has_roles')
            ->where('model_type', 'App\\User')
            ->update(['model_type' => 'App\\Models\\User']);
        $this->info("Updated $rolesCount records in model_has_roles");

        // 2. model_has_permissions (User mentioned there are no data, but safer to keep)
        $permsCount = DB::table('model_has_permissions')
            ->where('model_type', 'App\\User')
            ->update(['model_type' => 'App\\Models\\User']);
        $this->info("Updated $permsCount records in model_has_permissions");

        // 3. metas
        $metasCount = DB::table('metas')
            ->where('metable_type', 'App\\User')
            ->update(['metable_type' => 'App\\Models\\User']);
        $this->info("Updated $metasCount records in metas");

        // 4. personal_access_tokens
        $tokensCount = DB::table('personal_access_tokens')
            ->where('tokenable_type', 'App\\User')
            ->update(['tokenable_type' => 'App\\Models\\User']);
        $this->info("Updated $tokensCount records in personal_access_tokens");

        $this->info('Repair complete!');
    }
}
