<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Scout\Searchable;
class Product extends Model {
    use Searchable;
    protected $fillable = ["name","slug","reference","brand_id","category_id","short_description","long_description","price_fcfa","price_visible","status","is_featured","order","product_type","stock_status","stock_quantity"];
    protected $casts = ["price_visible" => "boolean","is_featured" => "boolean","price_fcfa" => "integer"];

    protected static function booted(): void
    {
        // Supprime explicitement chaque ProductMedia afin que son hook deleting
        // déclenche le nettoyage S3 (une FK CASCADE DB court-circuiterait l'event Eloquent).
        static::deleting(function (Product $product) {
            $product->media()->each(fn ($m) => $m->delete());
        });

        static::updated(function (Product $product) {
            if ($product->wasChanged('product_type') && $product->product_type !== 'reactif') {
                $product->reagentDetail?->delete();
            }
        });
    }

    /**
     * Colonnes interrogées par la recherche (Scout, driver database).
     * Matching par LIKE → correspondance partielle (ex. « micro » trouve « Microscope »),
     * adaptée à l'autocomplétion. Un index FULLTEXT existe aussi sur la table si besoin de pertinence.
     *
     * @return array<string, mixed>
     */
    public function toSearchableArray(): array
    {
        return [
            'name' => $this->name,
            'reference' => $this->reference,
            'short_description' => $this->short_description,
            'long_description' => strip_tags((string) $this->long_description),
        ];
    }

    /** Seuls les produits actifs sont recherchables. */
    public function shouldBeSearchable(): bool
    {
        return $this->status === 'actif';
    }
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function poles(): BelongsToMany { return $this->belongsToMany(Pole::class, "product_pole"); }
    public function specs(): HasMany { return $this->hasMany(ProductSpec::class)->orderBy("order"); }
    public function media(): HasMany { return $this->hasMany(ProductMedia::class)->orderBy("order"); }
    public function coverImage(): HasOne { return $this->hasOne(ProductMedia::class)->where("is_cover", true)->where("type", "image"); }
    public function reagentDetail(): HasOne { return $this->hasOne(ReagentDetail::class); }
    public function quotationItems(): HasMany { return $this->hasMany(QuotationItem::class); }
    public function relatedProducts(): BelongsToMany {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id')
            ->withPivot('order')
            ->orderBy('product_related.order');
    }
}