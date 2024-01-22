<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class WordPronunciation extends Model
{
    public $table = 'word_pronunciation';
    public $timestamps = false;

    protected $fillable = [
      'word_id',
      'language_id',
      'name'
    ];
}
