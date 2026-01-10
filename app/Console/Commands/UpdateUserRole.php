<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

class UpdateUserRole extends Command
{
    /**
    * The name and signature of the console command.
    *
    * @var string
    */
    protected $signature = 'orus:update-user-role {user_id} {role}';

    /**
    * The console command description.
    *
    * @var string
    */
    protected $description = 'Update a user\'s role by removing all existing roles and assigning a new one';

    /**
    * Execute the console command.
    */
    public function handle()
    {
        $userId = $this->argument('user_id');
        $roleName = $this->argument('role');

        // Validate inputs
        $validator = Validator::make([
            'user_id' => $userId,
            'role' => $roleName,
        ], [
            'user_id' => 'required|integer',
            'role' => 'required|string',
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return 1;
        }

        // Find the user
        $user = User::find($userId);
        
        if (!$user) {
            $this->error("User with ID {$userId} not found.");
            return 1;
        }
        // dd($user);

        // Check if the role exists
        if (!\Spatie\Permission\Models\Role::where('name', $roleName)->exists()) {
            $this->error("Role '{$roleName}' does not exist.");
            return 1;
        }

        try {
            // Remove all existing roles
            $user->roles()->detach();
            
            // Assign the new role
            $user->assignRole($roleName);
            
            $this->info("Successfully updated user #{$userId} to role: {$roleName}");
            return 0;
        } catch (\Exception $e) {
            $this->error("Error updating user role: " . $e->getMessage());
            return 1;
        }
    }
}

