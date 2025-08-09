<?php

namespace App\Models;

use Spatie\Permission\Models\Permission as SpatiePermission;

class CustomPermission extends SpatiePermission 
{
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->where('is_active',1);
    }

}

