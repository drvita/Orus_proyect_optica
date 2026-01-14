<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleAdmin = Role::firstOrCreate(["name" => "admin", "guard_name" => "api"]);
        $roleDoctor = Role::firstOrCreate(["name" => "doctor", "guard_name" => "api"]);
        $roleVentas = Role::firstOrCreate(["name" => "ventas", "guard_name" => "api"]);
        $roleBot = Role::firstOrCreate(["name" => "bot", "guard_name" => "api"]);
        // Atm
        Permission::firstOrCreate(["name" => "atm.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "atm.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "atm.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "atm.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "atm.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        // Brand
        Permission::firstOrCreate(["name" => "brand.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "brand.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "brand.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "brand.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "brand.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Category
        Permission::firstOrCreate(["name" => "category.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "category.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "category.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "category.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "category.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Config
        Permission::firstOrCreate(["name" => "config.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleVentas, $roleBot, $roleDoctor]);
        Permission::firstOrCreate(["name" => "config.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "config.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "config.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "config.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Contact
        Permission::firstOrCreate(["name" => "contact.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "contact.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "contact.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "contact.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "contact.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        // Exam
        Permission::firstOrCreate(["name" => "exam.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "exam.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "exam.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "exam.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "exam.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleDoctor]);
        // Messenger
        Permission::firstOrCreate(["name" => "messenger.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "messenger.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "messenger.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "messenger.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "messenger.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Order
        Permission::firstOrCreate(["name" => "order.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "order.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "order.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "order.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "order.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Payments
        Permission::firstOrCreate(["name" => "payment.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "payment.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "payment.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "payment.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "payment.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Sales
        Permission::firstOrCreate(["name" => "sale.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "sale.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "sale.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "sale.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "sale.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Sale item
        Permission::firstOrCreate(["name" => "saleItem.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "saleItem.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "saleItem.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "saleItem.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "saleItem.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Session
        Permission::firstOrCreate(["name" => "session.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "session.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "session.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "session.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "session.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Store item
        Permission::firstOrCreate(["name" => "store.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "store.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "store.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "store.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "store.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Store Branch
        Permission::firstOrCreate(["name" => "storeBranch.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "storeBranch.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "storeBranch.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "storeBranch.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::firstOrCreate(["name" => "storeBranch.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Store lot
        Permission::firstOrCreate(["name" => "storeLot.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "storeLot.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "storeLot.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "storeLot.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "storeLot.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Users
        Permission::firstOrCreate(["name" => "user.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "user.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "user.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "user.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleDoctor]);
        Permission::firstOrCreate(["name" => "user.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Auth
        Permission::firstOrCreate(["name" => "auth.access", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleVentas, $roleDoctor]);
        Permission::firstOrCreate(["name" => "auth.changeBranch", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleDoctor]);
        Permission::firstOrCreate(["name" => "auth.closeSession", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::firstOrCreate(["name" => "auth.changeRole", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);

        // Assign role to Users
        User::All()->except(1)->each(function (User $user) {
            switch ($user->rol) {
                case 0:
                    $user->assignRole("admin");
                    break;
                case 1:
                    $user->assignRole("ventas");
                    break;
                case 2:
                    $user->assignRole("doctor");
                    break;
                default:
                    break;
            }
        });
        // Assign role to bot
        $bot = User::find(1);
        if($bot){
            $bot->assignRole('bot');
        }
    }
}
