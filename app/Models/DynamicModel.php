<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class DynamicModel extends Model
{
    protected $connection = 'mie_projects';
    protected $guarded = [];
    public $timestamps = false;

    protected $table = null;

    public function setTableName(string $table): self
    {
        $this->table = $table;
        return $this;
    }

    public function getTable(): string
    {
        if (! $this->table) {
            throw new \Exception('Table name must be set using setTableName() before querying');
        }
        return $this->table;
    }

    public function newQuery(): Builder
    {
        return parent::newQuery()->from($this->getTable());
    }

    public function newModelQuery(): Builder
    {
        return parent::newModelQuery()->from($this->getTable());
    }

    public function newInstance($attributes = [], $exists = false): static
    {
        $instance = parent::newInstance($attributes, $exists);
        $instance->setTableName($this->table);
        return $instance;
    }

    public function replicate(array $except = null)
    {
        $clone = parent::replicate($except);
        $clone->setTableName($this->table);
        return $clone;
    }
}
