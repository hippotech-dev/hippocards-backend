<?php

namespace App\Models\Package;

use App\Enums\EWordSimilarType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedWord extends Model
{
    use HasFactory;

    public $table = "v3_related_words";

    protected $fillable = [
        "word_id",
        "related_word_id",
        "value",
        "translation",
        "type"
    ];

    protected $casts = [
        "type" => EWordSimilarType::class
    ];
}
