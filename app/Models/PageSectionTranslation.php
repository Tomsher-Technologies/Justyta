<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PageSectionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['page_section_id', 'lang', 'title', 'subtitle', 'description', 'button_text', 'button_link'];

    public function pageSection()
    {
        return $this->belongsTo(PageSection::class);
    }
}
