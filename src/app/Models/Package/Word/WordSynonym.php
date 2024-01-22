<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class WordSynonym extends Model
{
    public $table = 'word_synonym';
    public $timestamps = false;

    protected $fillable = [
      'word_id',
      'language_id',
      'synonym',
      'translation',
      'type'
    ];
}
