<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;
class ProductMedia extends Model {
    protected $fillable = ["product_id","type","path","alt","title","is_cover","order"];
    protected $casts = ["is_cover" => "boolean"];
    public function product(): BelongsTo { return $this->belongsTo(Product::class); }

    protected static function booted(): void
    {
        static::deleting(function (ProductMedia $pm) {
            if ($pm->path && ! str_starts_with($pm->path, 'http') && ! str_starts_with($pm->path, '/')) {
                try { Storage::disk('s3')->delete($pm->path); } catch (\Throwable) {}
            }
        });
    }
}