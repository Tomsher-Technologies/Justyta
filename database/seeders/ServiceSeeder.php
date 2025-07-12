<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ServiceSeeder extends Seeder
{
    public function run()
    {
        $services = [
            ['id' => 1, 'name' => 'Online Live Consultancy', 'slug' => 'online-live-consultancy', 'parent_id' => null, 'icon' => '/storage/services/image_1747751132_8590.png', 'sort_order' => 1],
            ['id' => 2, 'name' => 'Request Submission', 'slug' => 'request-submission', 'parent_id' => null, 'icon' => '/storage/services/image_1747750923_9480.png', 'sort_order' => 2],
            ['id' => 3, 'name' => 'Law Firm Services', 'slug' => 'law-firm-services', 'parent_id' => null, 'icon' => '/storage/services/image_1747751008_2894.png', 'sort_order' => 3],
            ['id' => 4, 'name' => 'Legal Translation', 'slug' => 'legal-translation', 'parent_id' => null, 'icon' => '/storage/services/image_1747751077_1206.png', 'sort_order' => 4],
            ['id' => 5, 'name' => 'Annual Retainer Agreement', 'slug' => 'annual-retainer-agreement', 'parent_id' => null, 'icon' => '/storage/services/image_1747751088_5610.png', 'sort_order' => 5],
            ['id' => 6, 'name' => 'Immigration Requests', 'slug' => 'immigration-requests', 'parent_id' => null, 'icon' => '/storage/services/image_1747751099_2496.png', 'sort_order' => 6],
            ['id' => 7, 'name' => 'Court Case Submission', 'slug' => 'court-case-submission', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807848_346.png', 'sort_order' => 1],
            ['id' => 8, 'name' => 'Criminal Complaint', 'slug' => 'criminal-complaint', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807864_9027.png', 'sort_order' => 2],
            ['id' => 9, 'name' => 'Power Of Attorney', 'slug' => 'power-of-attorney', 'parent_id' => 3, 'icon' => '/storage/services/image_1747808179_7248.png', 'sort_order' => 3],
            ['id' => 10, 'name' => 'Last Will & Testament', 'slug' => 'last-will-and-testament', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807890_8748.png', 'sort_order' => 4],
            ['id' => 11, 'name' => 'Memo Writing', 'slug' => 'memo-writing', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807906_4621.png', 'sort_order' => 5],
            ['id' => 12, 'name' => 'Expert Report', 'slug' => 'expert-report', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807918_4751.png', 'sort_order' => 6],
            ['id' => 13, 'name' => 'Contract Drafting', 'slug' => 'contract-drafting', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807929_7331.png', 'sort_order' => 7],
            ['id' => 14, 'name' => 'Company Setup', 'slug' => 'company-setup', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807942_1433.png', 'sort_order' => 8],
            ['id' => 15, 'name' => 'Escrow Accounts', 'slug' => 'escrow-accounts', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807957_5501.png', 'sort_order' => 9],
            ['id' => 16, 'name' => 'Debts Collection', 'slug' => 'debts-collection', 'parent_id' => 3, 'icon' => '/storage/services/image_1747807968_8421.png', 'sort_order' => 10],
        ];

        foreach ($services as &$service) {
            $service['status'] = 1;
            $service['created_at'] = Carbon::now();
            $service['updated_at'] = Carbon::now();
        }

        DB::table('services')->insert($services);
    }
}
