<?php

namespace App\Support;

/**
 * Liste curée d'icônes Material Symbols proposées dans le back-office (catégories, pôles).
 * Évite à l'utilisateur de connaître les noms techniques : il choisit un libellé en français.
 * La valeur stockée reste le nom de l'icône Material Symbols Outlined utilisé côté site.
 */
class MaterialIcons
{
    /**
     * Options avec aperçu visuel de l'icône (glyphe Material Symbols + libellé).
     * À utiliser avec ->allowHtml() sur un Select. La police est chargée dans le panneau admin.
     *
     * @return array<string, string>
     */
    public static function htmlOptions(): array
    {
        $out = [];
        foreach (self::options() as $icon => $label) {
            $out[$icon] = '<span class="material-symbols-outlined" style="vertical-align:-6px;margin-right:8px;font-size:20px;">'
                . e($icon) . '</span>' . e($label);
        }

        return $out;
    }

    /** @return array<string, string> [nom_icone => libellé FR] */
    public static function options(): array
    {
        return [
            // Diagnostic & spécialités médicales
            'cardiology' => 'Cardiologie (cœur)',
            'monitor_heart' => 'Monitoring / Soins critiques',
            'ecg_heart' => 'Électrocardiographie',
            'stethoscope' => 'Consultation / Stéthoscope',
            'pulmonology' => 'Pneumologie (poumons)',
            'neurology' => 'Neurologie (cerveau)',
            'orthopedics' => 'Orthopédie',
            'dentistry' => 'Dentaire',
            'visibility' => 'Ophtalmologie (œil)',
            'hearing' => 'ORL / Audition',
            'nephrology' => 'Néphrologie (rein)',
            'gastroenterology' => 'Gastro-entérologie',
            'pregnant_woman' => 'Gynécologie / Maternité',
            'child_care' => 'Néonatologie / Pédiatrie',
            'psychology' => 'Neurologie / Psychologie',

            // Imagerie & équipements
            'radiology' => 'Radiologie / Imagerie',
            'mri' => 'Scanner / IRM',
            'medical_services' => 'Services médicaux',
            'local_hospital' => 'Hôpital',
            'bed' => 'Mobilier / Lits',
            'medical_information' => 'Instrumentation',
            'emergency' => 'Urgence',
            'healing' => 'Soins & pansements',
            'vaccines' => 'Injection / Vaccins',
            'masks' => 'Protection',
            'sanitizer' => 'Hygiène / Désinfection',

            // Laboratoire & biologie / réactifs ABM
            'biotech' => 'Biologie moléculaire',
            'science' => 'Laboratoire / Culture cellulaire',
            'coronavirus' => 'Microbiologie',
            'genetics' => 'Génomique / Séquençage',
            'precision_manufacturing' => 'CRISPR / Édition génique',
            'bloodtype' => 'Hématologie / Sang',
            'water_drop' => 'Traitement de l\'eau / Dialyse',
            'ac_unit' => 'Chaîne du froid',

            // Générique
            'category' => 'Générique (catégorie)',
            'inventory_2' => 'Stock / Consommables',
            'school' => 'Formation',
            'pets' => 'Vétérinaire',
        ];
    }
}
