<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class WordImagination extends Model
{
    public $table = 'word_imagination';
    public $timestamp = false;

    protected $fillable = [
        'word_id',
        'language_id',
        'user_id',
        'baseklass_id',
        'imagination_id'
    ];

    public function imagination()
    {
        return $this->belongsTo(Imagination::class, "imagination_id");
    }
}
