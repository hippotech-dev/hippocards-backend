<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class WordTranslation extends Model
{
    public $table = 'word_translation';
    public $timestamps = false;

    protected $fillable = [
      'word_id',
      'language_id',
      'name',
      'updated_user'
    ];
}
