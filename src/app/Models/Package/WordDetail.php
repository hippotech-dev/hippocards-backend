<?php

namespace App\Models\Package;

use App\Enums\EPartOfSpeech;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordDetail extends Model
{
    use HasFactory;

    public $table = "v3_word_details";

    protected $fillable = [
        "word_id",
        "translation",
        "keyword",
        "pronunciation",
        "hiragana",
        "part_of_speech"
    ];

    protected $casts = [
        "part_of_speech" => EPartOfSpeech::class
    ];
}
