<?php

namespace App\Models\Package\Word;

use App\Enums\ESentenceType;
use App\Models\Package\WordDetail;
use App\Models\Package\WordImage;
use App\Models\Utility\Sentence;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Word extends Model
{
    use SoftDeletes;

    protected $table = 'word';
    protected $fillable = [
        'word',
        'sort',
        'sort2',
        'is_active',
        "update_type",
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

    public function sentences()
    {
        return $this->morphMany(Sentence::class, "object", "object_type", "object_id");
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
