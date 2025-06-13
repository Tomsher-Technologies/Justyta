<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Emirate;
use App\Models\EmirateTranslation;

class EmiratesSeeder extends Seeder
{
    public function run()
    {
        $emirates = [
            ['en' => 'Abu Dhabi',     'ar' => 'أبو ظبي'],
            ['en' => 'Dubai',         'ar' => 'دبي'],
            ['en' => 'Sharjah',       'ar' => 'الشارقة'],
            ['en' => 'Ajman',         'ar' => 'عجمان'],
            ['en' => 'Umm Al Quwain', 'ar' => 'أم القيوين'],
            ['en' => 'Ras Al Khaimah','ar' => 'رأس الخيمة'],
            ['en' => 'Fujairah',      'ar' => 'الفجيرة'],
        ];

        foreach ($emirates as $entry) {
            $emirate = Emirate::create();
            foreach ($entry as $lang => $name) {
                if($lang == 'en'){
                    $emirate->name = $name;
                    $emirate->save();
                }
                EmirateTranslation::create([
                    'emirate_id' => $emirate->id,
                    'lang' => $lang,
                    'name' => $name,
                ]);
            }
        }
    }
}
