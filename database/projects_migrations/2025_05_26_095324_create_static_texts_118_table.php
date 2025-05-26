<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mie_projects')->create('static_texts_118', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('key', 255);
            $table->string('value', 255);
            
        });
    }

    public function down()
    {
        Schema::connection('mie_projects')->dropIfExists('static_texts_118');
    }
};