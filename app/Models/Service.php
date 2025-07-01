<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = ['name', 'slug', 'parent_id', 'icon', 'sort_order', 'status', 'created_at','payment_active', 'service_fee', 'govt_fee', 'tax','total_amount'];

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

    public function translation($locale = null)
    {
        $locale = $locale ?? app()->getLocale();
        return $this->translations->where('lang', $locale)->first();
    }

    public function getTranslatedNameAttribute()
    {
        return $this->translation()?->title ?? $this->translations->first()?->title ?? 'Service';
    }
}
