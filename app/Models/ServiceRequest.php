<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;

class ServiceRequest extends Model
{
    use HasFactory;

    protected $table = 'service_requests';

    protected $fillable = [
        'user_id',
        'service_id',
        'service_slug',
        'reference_code',
        'status',
        'source',
        'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    // Relationships

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public static function generateReferenceCode($service)
    {
        $prefix = strtoupper(Str::slug($service->short_code, '-'));

        // Find last reference for this service
        $lastCode = self::where('service_id', $service->id)
            ->orderBy('id', 'desc')
            ->value('reference_code');

        $nextNumber = 1;
        if ($lastCode) {
            preg_match('/(\d+)$/', $lastCode, $matches);
            $nextNumber = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
        }

        return $prefix . '-' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}