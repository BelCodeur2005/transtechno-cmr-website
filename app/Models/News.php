<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class News extends Model {
    protected $fillable = ["title","slug","excerpt","content","cover_image_path","author_id","news_category_id","status","published_at"];
    protected $casts = ["published_at" => "datetime"];
    public function author(): BelongsTo { return $this->belongsTo(User::class, "author_id"); }
    public function category(): BelongsTo { return $this->belongsTo(NewsCategory::class, "news_category_id"); }
    public function tags(): BelongsToMany { return $this->belongsToMany(NewsTag::class, "news_news_tag"); }
}