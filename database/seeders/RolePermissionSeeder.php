<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Affecte des permissions par défaut aux rôles métier (modèle B).
 * super_admin est géré par le gate Shield (accès total) ; client_b2b n'accède pas au panel.
 */
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $perms = Permission::all();

        // Extrait le "sujet" d'une permission (ex. view_any_sav::ticket → sav::ticket).
        $subjectOf = fn (string $name): string => preg_replace(
            '/^(view_any|view|create|update|delete_any|delete|restore_any|restore|force_delete_any|force_delete|reorder|replicate)_/',
            '',
            $name
        );

        $bySubjects = fn (array $subjects): array => $perms
            ->filter(fn ($p) => in_array($subjectOf($p->name), $subjects, true))
            ->pluck('name')->all();

        $allWidgets = $perms->filter(fn ($p) => str_starts_with($p->name, 'widget_'))->pluck('name')->all();

        $map = [
            // Commercial : cotations, catalogue, demandes de projet, messages, clients.
            'commercial' => array_merge(
                $bySubjects(['quotation', 'pole::project::request', 'contact::message', 'product', 'category', 'brand', 'pole', 'client']),
                $allWidgets,
            ),

            // Technicien SAV : tickets SAV + parc installé + consultation produits & clients.
            'technicien_sav' => array_merge(
                $bySubjects(['sav::ticket', 'equipment::inventory']),
                ['view_any_product', 'view_product', 'view_any_client', 'view_client', 'widget_DashboardStats', 'widget_OperationsPanel'],
            ),

            // Éditeur de contenu : actualités, catégories, médiathèque + lecture messages.
            'editeur_contenu' => array_merge(
                $bySubjects(['news', 'news::category', 'media']),
                ['page_Mediatheque', 'view_any_contact::message', 'view_contact::message', 'widget_DashboardStats'],
            ),

            // Magasinier : gestion du stock produits.
            'magasinier' => [
                'view_any_product', 'view_product', 'update_product',
                'widget_DashboardStats', 'widget_OperationsPanel',
            ],
        ];

        foreach ($map as $roleName => $permissions) {
            $role = Role::where('name', $roleName)->first();
            if ($role) {
                $valid = array_values(array_intersect($permissions, $perms->pluck('name')->all()));
                $role->syncPermissions($valid);
                $this->command?->info("Rôle {$roleName} : " . count($valid) . ' permissions.');
            }
        }

        // super_admin : toutes les permissions (visible aussi dans la matrice).
        if ($superAdmin = Role::where('name', 'super_admin')->first()) {
            $superAdmin->syncPermissions($perms->pluck('name')->all());
            $this->command?->info('Rôle super_admin : toutes les permissions.');
        }
    }
}
