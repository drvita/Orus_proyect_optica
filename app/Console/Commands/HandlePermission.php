<?php

namespace App\Console\Commands;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Console\Command;

class HandlePermission extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orus:permission {role} {permission}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add permission to a role';

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
        if (!$this->argument('role')) {
            $this->info('Es necesario establecer el role!');
            return 0;
        }
        $roleParam = (string) $this->argument('role');
        if (!$this->argument('permission')) {
            $this->info('Es necesario establecer el permiso!');
            return 0;
        }
        $permissionParam = (string) $this->argument('permission');

        $this->info('Iniciando operaciones: ' . $roleParam . ' - ' . $permissionParam);
        $role = Role::findByName($roleParam, "api");

        if ($role) {
            $permission = Permission::where("name", $permissionParam)->where("guard_name", "api")->first();
            $allPermissions = $role->permissions->toArray();
            $itemsPermission = [$permissionParam];

            if (!$permission) {
                Permission::create(["name" => $permissionParam, "guard_name" => "api"]); // "auth.changeRole"
            }

            if ($allPermissions) {
                for ($i = 0; $i < count($allPermissions); $i++) {
                    $itemsPermission[] = $allPermissions[$i]["name"];
                }
            }

            $role->syncPermissions($itemsPermission);
            // TODO: Finished
            dd($role->permissions->toArray());
        }


        return 0;
    }
}