<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('permissions')->insert([
            ['id' => 1, 'parent_id' => null, 'name' => 'manage_roles', 'title' => 'Manage Roles', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 2, 'parent_id' => 1, 'name' => 'view_role', 'title' => 'View Roles', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 3, 'parent_id' => 1, 'name' => 'add_role', 'title' => 'Add Roles', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 4, 'parent_id' => 1, 'name' => 'edit_role', 'title' => 'Edit Roles', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 5, 'parent_id' => null, 'name' => 'manage_staff', 'title' => 'Manage Staff', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 6, 'parent_id' => 5, 'name' => 'view_staff', 'title' => 'View Staff', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 7, 'parent_id' => 5, 'name' => 'add_staff', 'title' => 'Add Staff', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 8, 'parent_id' => 5, 'name' => 'edit_staff', 'title' => 'Edit Staff', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 9, 'parent_id' => null, 'name' => 'manage_plan', 'title' => 'Manage Plan', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 10, 'parent_id' => 9, 'name' => 'view_plan', 'title' => 'View Plan', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 11, 'parent_id' => 9, 'name' => 'add_plan', 'title' => 'Add Plan', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 12, 'parent_id' => 9, 'name' => 'edit_plan', 'title' => 'Edit Plan', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 13, 'parent_id' => null, 'name' => 'manage_vendors', 'title' => 'Manage Law Firms', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 14, 'parent_id' => 13, 'name' => 'view_vendor', 'title' => 'View Law Firms', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 15, 'parent_id' => 13, 'name' => 'add_vendor', 'title' => 'Add Law Firms', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 16, 'parent_id' => 13, 'name' => 'edit_vendor', 'title' => 'Edit Law Firms', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 17, 'parent_id' => null, 'name' => 'manage_dropdown_option', 'title' => 'Manage Dropdown Items', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 18, 'parent_id' => 17, 'name' => 'view_dropdown_option', 'title' => 'View Dropdown Items', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 19, 'parent_id' => 17, 'name' => 'add_dropdown_option', 'title' => 'Add Dropdown Items', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 20, 'parent_id' => 17, 'name' => 'edit_dropdown_option', 'title' => 'Edit Dropdown Items', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 21, 'parent_id' => null, 'name' => 'manage_document_type', 'title' => 'Manage Document Type', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 22, 'parent_id' => 21, 'name' => 'view_document_type', 'title' => 'View Document Type', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 23, 'parent_id' => 21, 'name' => 'add_document_type', 'title' => 'Add Document Type', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 24, 'parent_id' => 21, 'name' => 'edit_document_type', 'title' => 'Edit Document Type', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 25, 'parent_id' => null, 'name' => 'manage_service', 'title' => 'Manage Services', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 26, 'parent_id' => 25, 'name' => 'view_service', 'title' => 'View Service', 'guard_name' => 'web', 'is_active' => 1],
            ['id' => 27, 'parent_id' => 25, 'name' => 'add_service', 'title' => 'Add Service', 'guard_name' => 'web', 'is_active' => 0],
            ['id' => 28, 'parent_id' => 25, 'name' => 'edit_service', 'title' => 'Edit Service', 'guard_name' => 'web', 'is_active' => 1],
        ]);
    }
}
