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
        $roles = ["super_admin","commercial","technicien_sav","editeur_contenu","magasinier","client_b2b"];
        foreach ($roles as $role) {
            Role::firstOrCreate(["name" => $role, "guard_name" => "web"]);
        }

        if (! app()->environment("production")) {
            $password = env("SEED_ADMIN_PASSWORD")
                ?: throw new \RuntimeException("SEED_ADMIN_PASSWORD not set in .env");

            $kamga = User::firstOrCreate(
                ["email" => "office@transtechno-cmr.com"],
                ["name" => "Kamga", "password" => Hash::make($password)]
            );
            $kamga->assignRole("super_admin");
            $kamga->assignRole("commercial");
        }
        }
}
