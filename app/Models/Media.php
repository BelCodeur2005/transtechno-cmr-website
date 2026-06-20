<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model {
    protected $fillable = ["filename","path","alt","title","caption","description","mime_type","size"];

    protected static function booted(): void
    {
        static::deleting(function (Media $media) {
            if ($media->path && ! str_starts_with($media->path, 'http') && ! str_starts_with($media->path, '/')) {
                try { Storage::disk('s3')->delete($media->path); } catch (\Throwable) {}
            }
        });

        // Renseigne automatiquement filename/size/mime à partir du fichier uploadé.
        static::saving(function (Media $media) {
            if (! $media->path) {
                return;
            }

            if (empty($media->filename)) {
                $media->filename = basename($media->path);
            }

            try {
                $disk = Storage::disk('s3');
                if ($disk->exists($media->path)) {
                    $media->size = $disk->size($media->path);
                    $media->mime_type = $disk->mimeType($media->path) ?: $media->mime_type;
                }
            } catch (\Throwable) {
                // S3 metadata check is non-critical — skip on failure
            }
        });
    }

    /** URL publique du média. */
    public function url(): ?string
    {
        if (! $this->path) {
            return null;
        }

        if (str_starts_with($this->path, 'http') || str_starts_with($this->path, '/')) {
            return $this->path;
        }

        return url('/media-proxy/' . $this->path);
    }

    /** Taille lisible (Ko / Mo). */
    public function humanSize(): ?string
    {
        if (! $this->size) {
            return null;
        }

        return $this->size >= 1048576
            ? number_format($this->size / 1048576, 1, ',', ' ') . ' Mo'
            : number_format($this->size / 1024, 0, ',', ' ') . ' Ko';
    }
}
