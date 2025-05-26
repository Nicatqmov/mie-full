<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mie_projects')->dropIfExists('blogs_118');
        Schema::connection('mie_projects')->create('blogs_118', function (Blueprint $table) {
            $table->id();
            $table->string('title', 255)->nullable();
            $table->text('description')->nullable();
            $table->string('image', 255)->nullable();
            $table->string('slug', 255)->nullable();
            $table->string('test', 255)->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mie_projects')->table('blogs_118', function (Blueprint $table) {
            $table->dropColumn('blogs_118');
        });
    }
};