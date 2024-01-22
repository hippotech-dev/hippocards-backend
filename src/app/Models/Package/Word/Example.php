<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class Example extends Model
{
    public $table = 'example';
    public $timestamps = false;

    protected $fillable = [
      'name'
    ];
}
