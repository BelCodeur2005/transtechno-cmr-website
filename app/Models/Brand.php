<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Brand extends Model {
    protected $fillable = ["name","slug","logo_path","description","website","is_active","order"];
    protected $casts = ["is_active" => "boolean"];
    public function products(): HasMany { return $this->hasMany(Product::class); }
}