<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\Entity;
use App\Models\Fields;

class MigrationService
{
    public function generateAndRunMigration($projectID)
    {
        try {
            $entities = Entity::where('project_id', $projectID)->get();
            $createdMigrations = [];

            foreach ($entities as $entity) {
                $tableName = $entity->table_name;

                if (!Schema::connection('mie_projects')->hasTable($tableName)) {
                    $migrationFile = $this->createMigrationFile($tableName, $entity, $projectID);
                    $createdMigrations[] = $migrationFile;
                }
            }

            sleep(2); 
            
            try {
                Artisan::call('migrate', ['--force' => true, '--database' => 'mie_projects', '--path' => 'database/projects_migrations']);
            } catch (\Exception $e) {
                // If migration fails, rollback any created tables
                foreach ($entities as $entity) {
                    $tableName = $entity->table_name;
                    if (Schema::connection('mie_projects')->hasTable($tableName)) {
                        Schema::connection('mie_projects')->drop($tableName);
                    }
                }
                
                // Clean up created migration files
                foreach ($createdMigrations as $migrationFile) {
                    if (File::exists($migrationFile)) {
                        File::delete($migrationFile);
                    }
                }
                
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error("Migration failed for project {$projectID}: " . $e->getMessage());
            throw $e;
        }
    }

    private function createMigrationFile($tableName, $entity, $projectID)
    {
        if (!File::exists(database_path('projects_migrations'))) {
            File::makeDirectory(database_path('projects_migrations'), 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $migrationFile = database_path("projects_migrations/{$timestamp}_create_{$tableName}_table.php");

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
                Schema::connection('mie_projects')->create('{$tableName}', function (Blueprint \$table) {
                    \$table->id();
                    \$table->timestamps();
                    {$fieldDefinitions}
                });
            }
    
            public function down()
            {
                Schema::connection('mie_projects')->dropIfExists('{$tableName}');
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
                return "\$table->string('{$field->column_name}', 255)";
            case 'text':
                return "\$table->text('{$field->column_name}')";
            case 'integer':
                return "\$table->integer('{$field->column_name}')";
            case 'decimal':
                return "\$table->decimal('{$field->column_name}', 10, 2)";
            case 'boolean':
                return "\$table->boolean('{$field->column_name}')";
            case 'date':
                return "\$table->date('{$field->column_name}')";
            case 'datetime':
                return "\$table->dateTime('{$field->column_name}')";
            default:
                return "\$table->string('{$field->column_name}', 255)";
        }
    }
}
