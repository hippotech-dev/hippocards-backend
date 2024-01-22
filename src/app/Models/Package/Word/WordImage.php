<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class WordImage extends Model
{
    protected $table = 'word_image';

    protected $fillable = [
      'word_id',
      'language_id',
      'user_id',
      'baseklass_id',
      'image_id',
      'is_image',
      'tag',
    ];

    public function image()
    {
        return $this->belongsTo(Image::class, "image_id");
    }
}
