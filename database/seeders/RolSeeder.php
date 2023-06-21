<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
class RolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $roleEmpacador = Role::create(['name' => 'Picking']);
        $roleDespachador = Role::create(['name' => 'Packing']);
        $roleDespachador = Role::create(['name' => 'Delivery ']);
        $roleAdmin = Role::create(['name' => 'Admin']);
        $permission = Permission::create(['name' => 'orders']);
        
    }
}
