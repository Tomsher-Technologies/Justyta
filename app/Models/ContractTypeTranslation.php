<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractTypeTranslation extends Model
{
    protected $fillable = ['contract_type_id', 'lang', 'name'];

    public function contractType()
    {
        return $this->belongsTo(ContractType::class);
    }
}