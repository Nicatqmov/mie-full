<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Pest\Arch\Blueprint;

class DataTypeModelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('data_type_models')->truncate();
        DB::table('data_type_models')->insert([
            ['name' => 'String',
            'type' => 'string',],
            ['name' => 'Text',
            'type' => 'text',],
            ['name' => 'Boolean',
            'type' => 'boolean',],
            ['name' => 'Image',
            'type' => 'image',],
            ['name' => 'Date',
            'type' => 'date',]
        ]);
    }
}
