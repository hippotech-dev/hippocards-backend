<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class WordKeyword extends Model
{
    public $table = 'word_keyword';
    public $timestamps = true;

    protected $fillable = [
      'word_id',
      'language_id',
      'user_id',
      'baseklass_id',
      'keyword_id'
    ];

    public function keyword()
    {
        return $this->belongsTo(Keyword::class, "keyword_id");
    }
}
