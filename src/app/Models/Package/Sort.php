<?php

namespace App\Models\Package;

use App\Models\Package\Word\Word;
use Illuminate\Database\Eloquent\Model;

class Sort extends Model
{
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
