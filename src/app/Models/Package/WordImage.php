<?php

namespace App\Models\Package;

use App\Enums\EWordImageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WordImage extends Model
{
    use HasFactory;

    public $table = "v3_word_images";

    protected $fillable = [
        "word_id",
        "name",
        "path",
        "type",
        "v3_asset_id"
    ];

    protected $casts = [
        "type" => EWordImageType::class
    ];
}
