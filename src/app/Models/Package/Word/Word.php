<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

// word update_type number 1-image, 2-tr, 3-kw, 5-imagination, 6-example, 7-ex_t, 8-ex_word, 9-gif, 10-sound
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

    public function wordImage()
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
}
