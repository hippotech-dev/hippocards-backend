<?php

namespace App\Models\Package;

use App\Models\Package\Word\Word;
use App\Models\Utility\Favorite;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sort extends Model
{
    use SoftDeletes;

    protected $table = 'sort';

    protected $fillable = [
        'word_id',
        'language_id',
        'user_id',
        'sort_word',
        'baseklass_id'
    ];

    /**
     * Relations
     */

    public function word()
    {
        return $this->hasOne(Word::class, "id", "word_id");
    }

    public function package()
    {
        return $this->belongsTo(Baseklass::class, "baseklass_id");
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, "object", "object_type", "object_id");
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query
            ->whereNotNull("baseklass_id")
            ->whereHas("package", fn ($subQuery) => $subQuery->active());
    }
}
