<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
class Product extends Model {
    protected $fillable = ["name","slug","reference","brand_id","category_id","short_description","long_description","price_fcfa","price_visible","status","is_featured","order","product_type","stock_status","stock_quantity"];
    protected $casts = ["price_visible" => "boolean","is_featured" => "boolean","price_fcfa" => "integer"];
    public function brand(): BelongsTo { return $this->belongsTo(Brand::class); }
    public function category(): BelongsTo { return $this->belongsTo(Category::class); }
    public function poles(): BelongsToMany { return $this->belongsToMany(Pole::class, "product_pole"); }
    public function specs(): HasMany { return $this->hasMany(ProductSpec::class)->orderBy("order"); }
    public function media(): HasMany { return $this->hasMany(ProductMedia::class)->orderBy("order"); }
    public function coverImage(): HasOne { return $this->hasOne(ProductMedia::class)->where("is_cover", true)->where("type", "image"); }
    public function reagentDetail(): HasOne { return $this->hasOne(ReagentDetail::class); }
    public function quotationItems(): HasMany { return $this->hasMany(QuotationItem::class); }
}