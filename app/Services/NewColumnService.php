<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\Entity;
use App\Models\Fields;

class NewColumnService
{
    public function updateTableColumns($entityID)
    {
        try {
            $entity = Entity::findorfail($entityID);
            $tableName = $entity->table_name;

            if (Schema::connection('mie_projects')->hasTable($tableName)) {
                $migrationFile = $this->createMigrationFile($tableName, $entity);
            }

            sleep(2); 
            
            try {
                Artisan::call('migrate', ['--force' => true, '--database' => 'mie_projects', '--path' => 'database/projects_migrations']);
            } catch (\Exception $e) {
                throw $e;
            }
        } catch (\Exception $e) {
            throw $e;
        }
    }

    private function createMigrationFile($tableName, $entity)
    {
        if (!File::exists(database_path('projects_migrations'))) {
            File::makeDirectory(database_path('projects_migrations'), 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $migrationFile = database_path("projects_migrations/{$timestamp}_update_{$tableName}_table.php");

        $fields = Fields::where('entity_id', $entity->id)->get();
        $fieldDefinitions = '';

        foreach ($fields as $field) {
            $fieldDefinitions .= $this->getFieldDefinition($field) . ";\n            ";
        }

        $migrationContent = <<<PHP
        <?php
        
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;
    
        return new class extends Migration {
            public function up()
            {
                Schema::connection('mie_projects')->dropIfExists('{$tableName}');
                Schema::connection('mie_projects')->create('{$tableName}', function (Blueprint \$table) {
                    \$table->id();
                    {$fieldDefinitions}
                    \$table->timestamps();
                });
            }
    
            public function down()
            {
                Schema::connection('mie_projects')->table('{$tableName}', function (Blueprint \$table) {
                    \$table->dropColumn('{$tableName}');
                });
            }
        };
        PHP;

        File::put($migrationFile, $migrationContent);

        // Store the migration file path in the entity record
        $entity->update([
            'migration_file' => "{$timestamp}_create_{$tableName}_table.php"
        ]);

        return $migrationFile;
    }

    private function getFieldDefinition($field)
    {
        switch ($field->dataType->type) {
            case 'string':
                return "\$table->string('{$field->column_name}', 255)->nullable()";
            case 'text':
                return "\$table->text('{$field->column_name}')->nullable()";
            case 'integer':
                return "\$table->integer('{$field->column_name}')->nullable()";
            case 'decimal':
                return "\$table->decimal('{$field->column_name}', 10, 2)->nullable()";
            case 'boolean':
                return "\$table->boolean('{$field->column_name}')->nullable()";
            case 'date':
                return "\$table->date('{$field->column_name}')->nullable()";
            case 'datetime':
                return "\$table->dateTime('{$field->column_name}')->nullable()";
            default:
                return "\$table->string('{$field->column_name}', 255)->nullable()";
        }
    }
}
