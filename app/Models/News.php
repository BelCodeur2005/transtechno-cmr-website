<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;
class News extends Model {
    protected $fillable = ["title","slug","excerpt","meta_title","meta_description","content","cover_image_path","author_id","news_category_id","status","published_at"];
    protected $casts = ["published_at" => "datetime"];
    public function author(): BelongsTo { return $this->belongsTo(User::class, "author_id"); }
    public function category(): BelongsTo { return $this->belongsTo(NewsCategory::class, "news_category_id"); }
    public function tags(): BelongsToMany { return $this->belongsToMany(NewsTag::class, "news_news_tag"); }

    protected static function booted(): void
    {
        static::deleting(function (News $news) {
            $p = $news->cover_image_path;
            if ($p && ! str_starts_with($p, 'http') && ! str_starts_with($p, '/')) {
                try { Storage::disk('s3')->delete($p); } catch (\Throwable) {}
            }
        });
    }

    /** URL absolue de la couverture : URL externe, chemin public (/…) ou fichier sur le disque storage. */
    public function coverUrl(): ?string {
        $p = $this->cover_image_path;
        if (! $p) { return null; }
        if (str_starts_with($p, "http")) { return $p; }
        if (str_starts_with($p, "/")) { return url($p); }
        return url('/media-proxy/' . $p);
    }
}