<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Entity;

class Fields extends Model
{
    protected $fillable=[
        'entity_id',
        'name',
        'column_name',
        'type',
    ];

    public function entities(){
        return $this->hasMany(Entity::class);
    }

    public function dataType()
    {
        return $this->belongsTo(DataTypeModel::class, 'type', 'id');
    }

}
