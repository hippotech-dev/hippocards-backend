<?php

namespace App\Models\Package\Word;

use App\Enums\ESentenceType;
use App\Models\Package\WordDetail;
use App\Models\Package\WordImage;
use App\Models\Utility\Sentence;
use Illuminate\Database\Eloquent\Model;

class Word extends Model
{
    protected $table = 'word';
    protected $fillable = [
        'word',
        'sort',
        'sort2',
        'is_active',
        "sound",
        "mp3"
    ];

    /**
     * Relations
     */

    public function mainDetail()
    {
        return $this->hasOne(WordDetail::class, "word_id");
    }

    public function images()
    {
        return $this->hasMany(WordImage::class, "word_id");
    }

    public function synonyms()
    {
        return $this->hasMany(WordSynonym::class, "word_id");
    }

    public function definitionSentences()
    {
        return $this->morphMany(Sentence::class, "object", "object_type", "object_id")->where("type", ESentenceType::DEFINITION);
    }

    public function imaginationSentences()
    {
        return $this->morphMany(Sentence::class, "object", "object_type", "object_id")->where("type", ESentenceType::IMAGINATION);
    }

    /**
     * Scopes
     */
}
