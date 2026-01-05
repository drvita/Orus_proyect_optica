<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    protected $tableNames;

    public function __construct()
    {
        $this->tableNames = config('permission.table_names');
        if (empty($this->tableNames)) {
            throw new \Exception('Error: config/permission.php not loaded. Run [php artisan config:clear] and try again.');
        }
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Model Has Permissions
        Schema::create($this->tableNames['model_has_permissions'] . '_new', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            if (config('permission.teams')) {
                $table->unsignedBigInteger('team_id')->nullable();
            }

            $table->primary(['permission_id', 'model_id', 'model_type']);
            if (config('permission.teams')) {
                $table->unique(['permission_id', 'model_id', 'model_type', 'team_id']);
            }
        });

        // Copiar datos
        if (config('permission.teams')) {
            DB::statement("INSERT INTO {$this->tableNames['model_has_permissions']}_new 
                        (permission_id, model_type, model_id, team_id)
                        SELECT permission_id, model_type, model_id, team_id 
                        FROM {$this->tableNames['model_has_permissions']}");
        } else {
            DB::statement("INSERT INTO {$this->tableNames['model_has_permissions']}_new 
                        (permission_id, model_type, model_id)
                        SELECT permission_id, model_type, model_id 
                        FROM {$this->tableNames['model_has_permissions']}");
        }

        // Eliminar tabla vieja y renombrar la nueva
        Schema::drop($this->tableNames['model_has_permissions']);
        Schema::rename($this->tableNames['model_has_permissions'] . '_new', $this->tableNames['model_has_permissions']);

        // 2. Model Has Roles
        Schema::create($this->tableNames['model_has_roles'] . '_new', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id');
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');

            if (config('permission.teams')) {
                $table->unsignedBigInteger('team_id')->nullable();
            }

            $table->primary(['role_id', 'model_id', 'model_type']);
            if (config('permission.teams')) {
                $table->unique(['role_id', 'model_id', 'model_type', 'team_id']);
            }
        });

        // Copiar datos
        if (config('permission.teams')) {
            DB::statement("INSERT INTO {$this->tableNames['model_has_roles']}_new 
                        (role_id, model_type, model_id, team_id)
                        SELECT role_id, model_type, model_id, team_id 
                        FROM {$this->tableNames['model_has_roles']}");
        } else {
            DB::statement("INSERT INTO {$this->tableNames['model_has_roles']}_new 
                        (role_id, model_type, model_id)
                        SELECT role_id, model_type, model_id 
                        FROM {$this->tableNames['model_has_roles']}");
        }

        // Eliminar tabla vieja y renombrar la nueva
        Schema::drop($this->tableNames['model_has_roles']);
        Schema::rename($this->tableNames['model_has_roles'] . '_new', $this->tableNames['model_has_roles']);

        // 3. Role Has Permissions
        Schema::create($this->tableNames['role_has_permissions'] . '_new', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id');

            $table->primary(['permission_id', 'role_id']);
        });

        // Copiar datos
        DB::statement("INSERT INTO {$this->tableNames['role_has_permissions']}_new 
                    (permission_id, role_id)
                    SELECT permission_id, role_id 
                    FROM {$this->tableNames['role_has_permissions']}");

        // Eliminar tabla vieja y renombrar la nueva
        Schema::drop($this->tableNames['role_has_permissions']);
        Schema::rename($this->tableNames['role_has_permissions'] . '_new', $this->tableNames['role_has_permissions']);

        // 4. Recrear Foreign Keys
        Schema::table($this->tableNames['model_has_permissions'], function (Blueprint $table) {
            $table->foreign('permission_id')
                ->references('id')
                ->on($this->tableNames['permissions'])
                ->onDelete('cascade');

            if (config('permission.teams')) {
                $table->foreign('team_id')
                    ->references('id')
                    ->on('teams')
                    ->onDelete('cascade');
            }
        });

        Schema::table($this->tableNames['model_has_roles'], function (Blueprint $table) {
            $table->foreign('role_id')
                ->references('id')
                ->on($this->tableNames['roles'])
                ->onDelete('cascade');

            if (config('permission.teams')) {
                $table->foreign('team_id')
                    ->references('id')
                    ->on('teams')
                    ->onDelete('cascade');
            }
        });

        Schema::table($this->tableNames['role_has_permissions'], function (Blueprint $table) {
            $table->foreign('permission_id')
                ->references('id')
                ->on($this->tableNames['permissions'])
                ->onDelete('cascade');
            $table->foreign('role_id')
                ->references('id')
                ->on($this->tableNames['roles'])
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // En caso de rollback, las tablas ya están en el formato correcto
        // No necesitamos hacer nada ya que las claves foráneas y los nombres de columnas
        // ya están configurados correctamente
    }
};
