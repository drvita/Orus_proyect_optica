<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ChangeRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleVentas = Role::where("name", "ventas")->first();
        // dd("Start", $roleVentas->permissions->toArray());
        Permission::where("name", "like", "brand.%")
            ->orWhere("name", "like", "category.%")
            ->orWhere("name", "like", "store.%")
            ->orWhere("name", "like", "storeBranch.%")
            ->orWhere("name", "like", "storeLot.%")
            ->get()
            ->each(function ($p) use ($roleVentas) {
                $has = $roleVentas->permissions()->where("name", $p->name)->first();

                if (!$has) {
                    $roleVentas->givePermissionTo($p->name);
                }
            });
    }
}
