<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Menu;

class RoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = Role::create(['name' => 'admin']);
        $guru = Role::create(['name' => 'guru']);
    
        $dashboard = Menu::create(['title' => 'Dashboard', 'route' => '/dashboard']);
        $siswa = Menu::create(['title' => 'Data Siswa', 'route' => '/siswa']);
        $nilai = Menu::create(['title' => 'Nilai', 'route' => '/nilai']);
    
        $admin->menus()->attach([$dashboard->id, $siswa->id, $nilai->id]);
        $guru->menus()->attach([$dashboard->id, $nilai->id]);
    }
    
}
