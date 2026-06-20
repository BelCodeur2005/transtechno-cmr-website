<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Cache;
class Category extends Model {
    protected $fillable = ["name","slug","type","description","icon","parent_id","order","is_active"];
    protected $casts = ["is_active" => "boolean"];
    public function parent(): BelongsTo { return $this->belongsTo(Category::class, "parent_id"); }
    public function children(): HasMany { return $this->hasMany(Category::class, "parent_id"); }
    public function products(): HasMany { return $this->hasMany(Product::class); }

    /** Vide le cache de navigation dès qu'une catégorie change (création, édition, suppression). */
    protected static function booted(): void
    {
        $flush = fn () => Cache::forget('nav.categories');
        static::saved($flush);
        static::deleted($flush);
    }

    /**
     * Catégories racines actives, groupées par hiérarchie (equipement / reactif), pour les méga-menus.
     * Résultat léger et mis en cache — partagé sur toutes les pages publiques.
     *
     * @return array{equipement: array<int, array>, reactif: array<int, array>}
     */
    public static function navMenus(): array
    {
        return Cache::rememberForever('nav.categories', function () {
            return static::query()
                ->where('is_active', true)
                ->whereNull('parent_id')
                ->orderBy('order')
                ->orderBy('name')
                ->get(['name', 'slug', 'icon', 'type', 'description'])
                ->groupBy('type')
                ->map(fn ($group) => $group->map(fn (Category $c) => [
                    'name' => $c->name,
                    'slug' => $c->slug,
                    'icon' => $c->icon,
                    'description' => $c->description,
                ])->values()->all())
                ->all() + ['equipement' => [], 'reactif' => []];
        });
    }
}