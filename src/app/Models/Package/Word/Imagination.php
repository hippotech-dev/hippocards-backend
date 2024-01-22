<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class Imagination extends Model
{
    public $table = 'imagination';
    public $timestamps = false;

    protected $fillable = [
      'name'
    ];
}
