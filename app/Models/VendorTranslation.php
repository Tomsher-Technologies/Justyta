<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorTranslation extends Model
{
    protected $fillable = ['vendor_id', 'lang', 'law_firm_name', 'about'];
}

