<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Entity;
class Project extends Model
{
    protected $fillable=[
        'name',
        'user_id',
        'status',
        'token',
    ];

    public function entities(){
        return $this->hasMany(Entity::class);
    }
}
