<?php

namespace App\Models\Utility;

use App\Models\Package\Baseklass;
use Illuminate\Database\Eloquent\Model;

class MainCategory extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "language_id", "name", "name_mn",
        "tag", "sort", "color", "type",
        "is_acitve", "icon_id", "object_type"
    ];

    public function packages()
    {
        return $this->hasMany(Baseklass::class);
    }
}
