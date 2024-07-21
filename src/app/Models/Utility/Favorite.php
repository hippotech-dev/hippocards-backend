<?php

namespace App\Models\Utility;

use App\Enums\EFavoriteType;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    protected $fillable = [
        "user_id",
        "object_id",
        "object_type",
        "type",
        "language_id"
    ];

    protected $casts = [
        "type" => EFavoriteType::class,
    ];

    /**
     * Relations
     */

    public function object()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }

    /**
     * Scopes
     */

    public function scopePackage($query)
    {
        return $query->with([ "object" ]);
    }

    public function scopeSort($query)
    {
        return $query->with([ "object.word.images" ]);
    }

    public function scopeArticle($query)
    {
        return $query->with([ "object" ]);
    }
}
