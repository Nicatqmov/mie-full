<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('entities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('table_name');
            $table->unsignedBigInteger('project_id');
            $table->timestamps();
            $table->string('migration_file')->nullable();
            $table->boolean('is_new')->default(true);
        });
    }

    public function down(): void {
        Schema::dropIfExists('entities');
    }
};
