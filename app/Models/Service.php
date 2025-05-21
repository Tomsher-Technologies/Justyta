<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'icon', 'sort_order', 'status', 'created_at'];

    public function translations()
    {
        return $this->hasMany(ServiceTranslation::class);
    }

    // Relationship to parent
    public function parent()
    {
        return $this->belongsTo(Service::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Service::class, 'parent_id');
    }
}
