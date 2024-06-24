<?php

namespace App\Models\Utility;

use App\Enums\ESentenceType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sentence extends Model
{
    use HasFactory;

    public $table = "v3_sentences";

    protected $fillable = [
        "value",
        "translation",
        "object_id",
        "object_type",
        "type",
        "v3_audio_asset_id",
        "language_id",
    ];

    protected $casts = [
        "type" => ESentenceType::class,
    ];

    /**
     * Relations
     */

    public function object()
    {
        return $this->morphTo("object", "object_type", "object_id");
    }

    public function audioAsset()
    {
        return $this->belongsTo(Asset::class, "v3_audio_asset_id");
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
