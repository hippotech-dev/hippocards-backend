<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    public $table = 'image';
    public $timestamps = false;

    protected $fillable = [
        'image',
        'tumbnail_img',
    ];
}
