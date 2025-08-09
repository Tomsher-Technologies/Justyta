<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrainingRequest extends Model
{
    protected $table = 'training_requests';

    protected $fillable = [
        'user_id',
        'emirate_id',
        'position',
        'start_date',
        'residency_status',
        'documents',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'documents' => 'array', 
    ];

     public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function emirate()
    {
        return $this->belongsTo(Emirate::class);
    }

    public function positionOption()
    {
        return $this->belongsTo(DropdownOption::class, 'position');
    }

    public function residencyStatusOption()
    {
        return $this->belongsTo(DropdownOption::class, 'residency_status');
    }
}
