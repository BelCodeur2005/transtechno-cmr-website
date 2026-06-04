<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class Pole extends Model {
    protected $fillable = ["name","slug","description","color","icon","is_active"];
    protected $casts = ["is_active" => "boolean"];
    public function products(): BelongsToMany { return $this->belongsToMany(Product::class, "product_pole"); }
}