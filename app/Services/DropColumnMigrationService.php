<?php

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use App\Models\Entity;
use App\Models\Fields;

class UpdateMigrationService
{


    // public function generateAndRunDropColumnMigration($entityIDs,$columns)
    // {
    //     try {
    //         $createdMigrations = [];
    //         foreach($entityIDs as $entityID){
    //             $entity = Entity::find($entityID);
    //             $tableName = $entity->table_name;
    //             if(in_array($tableName,$columns)){
    //                 $migrationFile = $this->dropColumnMigrationFile($tableName, $entity,$tableName);
    //                 $createdMigrations[] = $migrationFile;
    //             }
    //         }
    //             if (!Schema::connection('mie_projects')->hasTable($tableName)) {
    //                 $migrationFile = $this->dropColumnMigrationFile($tableName, $entity);
    //                 $createdMigrations[] = $migrationFile;
    //             }
    //         }

    //         sleep(2); 
            
    //         try {
    //             Artisan::call('migrate', ['--force' => true, '--database' => 'mie_projects', '--path' => 'database/projects_migrations']);
    //         } catch (\Exception $e) {
    //             // If migration fails, rollback any created tables
    //             foreach ($entities as $entity) {
    //                 $tableName = $entity->table_name;
    //                 if (Schema::connection('mie_projects')->hasTable($tableName)) {
    //                     Schema::connection('mie_projects')->drop($tableName);
    //                 }
    //             }
                
    //             // Clean up created migration files
    //             foreach ($createdMigrations as $migrationFile) {
    //                 if (File::exists($migrationFile)) {
    //                     File::delete($migrationFile);
    //                 }
    //             }
                
    //             throw $e;
    //         }
    //     } catch (\Exception $e) {
    //         \Log::error("Migration failed for project {$projectID}: " . $e->getMessage());
    //         throw $e;
    //     }
    // }

    public function dropColumnMigrationFile($tableName, $entity){
        if (!File::exists(database_path('projects_migrations'))) {
            File::makeDirectory(database_path('projects_migrations'), 0755, true);
        }

        $timestamp = date('Y_m_d_His');
        $migrationFile = database_path("projects_migrations/{$timestamp}_drop_column_{$tableName}_table.php");

        $fields = Fields::where('entity_id', $entity->id)->get();
        foreach($fields as $field){
            if($field->table_name == $tableName){
                $fieldDefinitions[] = $this->getDropColumnFieldDefinition($field);
            }
        }

        foreach ($fields as $field) {
            $fieldDefinitions[] = $this->getDropColumnFieldDefinition($field);
        }

        $migrationContent = <<<PHP
        <?php
        
        use Illuminate\Database\Migrations\Migration;
        use Illuminate\Database\Schema\Blueprint;
        use Illuminate\Support\Facades\Schema;
    
        return new class extends Migration {
            public function up()
            {
                Schema::table('{$tableName}', function (Blueprint \$table) {
                    {$fieldDefinitions[0]}
                });
            }
    
            public function down()
            {
                Schema::table('{$tableName}', function (Blueprint \$table) {
                    {$fieldDefinitions[1]}
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
    

 
    private function getDropColumnFieldDefinition($field){
        return "\$table->dropColumn('{$field->column_name}')";
    }

    // private function getUpdateColumnFieldDefinition($field){
    //     switch ($field->dataType->type) {
    //         case 'string':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->string('{$field->new_column_name}', 255)"];
    //         case 'text':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->text('{$field->new_column_name}')"];
    //         case 'integer':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->integer('{$field->new_column_name}')"];
    //         case 'decimal':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->decimal('{$field->new_column_name}', 10, 2)"];
    //         case 'boolean':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->boolean('{$field->new_column_name}')"];
    //         case 'date':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->date('{$field->new_column_name}')"];
    //         case 'datetime':
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->dateTime('{$field->new_column_name}')"];
    //         default:
    //             return ["\$table->dropColumn('{$field->old_column_name}')", "\$table->string('{$field->new_column_name}', 255)"];
    //     }
    // }
}