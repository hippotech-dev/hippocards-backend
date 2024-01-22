<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class PartOfSpeech extends Model
{
    public $table = "part_of_speech";
    public $timestamps = false;

    protected $fillable = [
        "pos_id", "word_id"
    ];
}
