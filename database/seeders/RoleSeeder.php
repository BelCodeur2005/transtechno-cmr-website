<?php
namespace Database\Seeders;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['super_admin','commercial','technicien_sav','editeur_contenu','magasinier','client_b2b'];
        foreach ($roles as $role) {
            Role::firstOrCreate(['name' => $role, 'guard_name' => 'web']);
        }
        $kamga = User::firstOrCreate(
            ['email' => 'transtechno.cm@gmail.com'],
            ['name' => 'Kamga', 'password' => Hash::make('Admin@2026!')]
        );
        $kamga->assignRole('super_admin');
        $kamga->assignRole('commercial');
    }
}