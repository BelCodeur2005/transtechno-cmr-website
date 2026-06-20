<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
class Brand extends Model {
    protected $fillable = ["name","slug","logo_path","description","website","is_active","order"];
    protected $casts = ["is_active" => "boolean"];
    public function products(): HasMany { return $this->hasMany(Product::class); }

    protected static function booted(): void
    {
        static::deleting(function (Brand $brand) {
            $p = $brand->logo_path;
            if ($p && ! str_starts_with($p, 'http') && ! str_starts_with($p, '/')) {
                try { Storage::disk('s3')->delete($p); } catch (\Throwable) {}
            }
        });
    }
}