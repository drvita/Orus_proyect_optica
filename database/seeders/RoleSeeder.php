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
        $roleAdmin = Role::create(["name" => "admin", "guard_name" => "api"]);
        $roleDoctor = Role::create(["name" => "doctor", "guard_name" => "api"]);
        $roleVentas = Role::create(["name" => "ventas", "guard_name" => "api"]);
        $roleBot = Role::create(["name" => "bot", "guard_name" => "api"]);
        // Atm
        Permission::create(["name" => "atm.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "atm.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "atm.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "atm.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "atm.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        // Brand
        Permission::create(["name" => "brand.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "brand.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "brand.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "brand.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "brand.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Category
        Permission::create(["name" => "category.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "category.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "category.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "category.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "category.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Config
        Permission::create(["name" => "config.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleVentas, $roleBot, $roleDoctor]);
        Permission::create(["name" => "config.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "config.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "config.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "config.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Contact
        Permission::create(["name" => "contact.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "contact.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "contact.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "contact.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "contact.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        // Exam
        Permission::create(["name" => "exam.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "exam.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "exam.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "exam.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "exam.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleDoctor]);
        // Messenger
        Permission::create(["name" => "messenger.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "messenger.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "messenger.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "messenger.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "messenger.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Order
        Permission::create(["name" => "order.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "order.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "order.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "order.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "order.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Payments
        Permission::create(["name" => "payment.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "payment.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "payment.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "payment.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "payment.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Sales
        Permission::create(["name" => "sale.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "sale.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "sale.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "sale.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "sale.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Sale item
        Permission::create(["name" => "saleItem.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "saleItem.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "saleItem.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "saleItem.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "saleItem.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Session
        Permission::create(["name" => "session.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "session.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "session.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "session.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "session.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Store item
        Permission::create(["name" => "store.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "store.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "store.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "store.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "store.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Store Branch
        Permission::create(["name" => "storeBranch.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "storeBranch.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "storeBranch.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "storeBranch.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas]);
        Permission::create(["name" => "storeBranch.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Store lot
        Permission::create(["name" => "storeLot.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "storeLot.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "storeLot.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "storeLot.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "storeLot.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Users
        Permission::create(["name" => "user.list", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "user.show", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "user.add", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "user.edit", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot, $roleDoctor]);
        Permission::create(["name" => "user.delete", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        // Auth
        Permission::create(["name" => "auth.access", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleVentas, $roleDoctor]);
        Permission::create(["name" => "auth.changeBranch", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleDoctor]);
        Permission::create(["name" => "auth.closeSession", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);
        Permission::create(["name" => "auth.changeRole", "guard_name" => "api"])->syncRoles([$roleAdmin, $roleBot]);

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
