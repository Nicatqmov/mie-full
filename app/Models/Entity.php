<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Project;
use App\Models\Fields;
class Entity extends Model
{
    protected $fillable=[
        'project_id',
        'name',
        'table_name',
        'migration_file',
    ];


    public function project(){
        return $this->belongsTo(Project::class);
    }

    public function fields(){
        return $this->hasMany(Fields::class);
    }
}
