<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('fields', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('column_name');
            $table->string('type');
            $table->unsignedBigInteger('entity_id');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('fields');
    }
};
