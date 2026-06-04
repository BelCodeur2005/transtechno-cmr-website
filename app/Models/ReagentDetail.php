<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class ReagentDetail extends Model {
    protected $fillable = ["product_id","storage_temp","conditionnement","volume_quantity","is_ivd","lot_number_example","expiry_note"];
    protected $casts = ["is_ivd" => "boolean"];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}