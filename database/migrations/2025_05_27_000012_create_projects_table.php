<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            $table->enum('status', ['active', 'deleting', 'deleted', 'creating', 'failed'])->default('active');
            $table->string('token');
        });
    }

    public function down(): void {
        Schema::dropIfExists('projects');
    }
};
