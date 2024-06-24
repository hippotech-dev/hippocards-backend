<?php

namespace App\Models\Package\Word;

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

    public function translation()
    {
        return $this->hasOne(WordTranslation::class, "word_id");
    }

    public function pronunciation()
    {
        return $this->hasOne(WordPronunciation::class, "word_id");
    }

    public function wordImaginations()
    {
        return $this->hasMany(WordImagination::class, "word_id");
    }

    public function exampleSentences()
    {
        return $this->hasMany(WordExample::class, "word_id");
    }

    public function wordKeyword()
    {
        return $this->hasMany(WordKeyword::class, "word_id");
    }

    public function wordImages()
    {
        return $this->hasMany(WordImage::class, "word_id");
    }

    public function pos()
    {
        return $this->hasOne(PartOfSpeech::class, "word_id");
    }

    public function synonyms()
    {
        return $this->hasMany(WordSynonym::class, "word_id");
    }

    public function sentences()
    {
        return $this->morphMany(Sentence::class, "object", "object_type", "object_id");
    }

    /**
     * Scopes
     */
}
