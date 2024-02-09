<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Model;

class SystemIcon extends Model
{
    public $timestamps = false;

    protected $fillable = [
        "name",
        "path"
    ];
}
