<?php

namespace App\Models\Utility;

use App\Enums\ECategoryType;
use App\Models\Package\Baseklass;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    use HasFactory;
    public $timestamps = false;

    protected $fillable = [
        "language_id", "name", "name_mn",
        "tag", "sort", "color", "type",
        "is_acitve", "icon_id", "object_type"
    ];

    protected $casts = [
        "object_type" => ECategoryType::class
    ];

    public function packages()
    {
        return $this->hasMany(Baseklass::class);
    }
}
