<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationAssignmentHistory extends Model
{
    protected $table = 'translation_assignment_histories';

    protected $fillable = [
        'request_id', 'translator_id', 'assigned_by', 'hours_per_page', 'admin_amount', 'translator_amount', 'total_amount', 'assigned_at','delivery_amount','tax'
    ];

    protected $dates = ['assigned_at'];

    public function request()
    {
        return $this->belongsTo(RequestLegalTranslation::class, 'request_id');
    }

    public function translator()
    {
        return $this->belongsTo(User::class, 'translator_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
