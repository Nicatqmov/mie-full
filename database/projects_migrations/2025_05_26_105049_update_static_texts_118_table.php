<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::connection('mie_projects')->dropIfExists('static_texts_118');
        Schema::connection('mie_projects')->create('static_texts_118', function (Blueprint $table) {
            $table->id();
            $table->string('key', 255)->nullable();
            $table->string('value', 255)->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::connection('mie_projects')->table('static_texts_118', function (Blueprint $table) {
            $table->dropColumn('static_texts_118');
        });
    }
};