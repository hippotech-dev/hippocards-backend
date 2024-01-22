<?php

namespace App\Models\Package\Word;

use App\Models\Utility\Language;
use Illuminate\Database\Eloquent\Model;
use DB;

class WordExample extends Model
{
    protected $table = 'word_example';

    public $timestamps = false;

    protected $fillable = [
      'word_id',
      'language_id',
      'user_id',
      'example_id',
      'baseklass_id',
      'type',
      "audio",
      'en_id'
    ];

    public function example()
    {
        return $this->belongsTo(Example::class, "example_id");
    }

    public function language()
    {
        return $this->belongsTo(Language::class, "language_id");
    }

    public function translation()
    {
        return $this->belongsTo(Example::class, "en_id");
    }
}
