<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    public $table = 'device';
    public $timestamps = false;

    protected $fillable = [
      'user_id',
      'device_id',
      'model',
      'login_type',
      'active_at'
    ];
}
