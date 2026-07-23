<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class QuotationItem extends Model {
    protected $fillable = ["quotation_id","sort_order","product_id","custom_label","brand_name","quantity","unit_price_fcfa","is_locked"];
    protected $casts = ["is_locked" => "boolean"];
    public function quotation(): BelongsTo { return $this->belongsTo(Quotation::class); }
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function totalLine(): int { return $this->quantity * $this->unit_price_fcfa; }
}