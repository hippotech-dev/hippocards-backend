<?php

namespace App\Models\Package\Word;

use Illuminate\Database\Eloquent\Model;

class Keyword extends Model
{
    public $table = 'keyword';
    public $timestamps = false;

    protected $fillable = [
      'name'
    ];

}
