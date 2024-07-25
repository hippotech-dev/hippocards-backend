<?php

namespace App\Models\Package;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserCustomWordDetail extends Model
{
    use HasFactory;

    public $table = "v3_user_custom_word_details";

    protected $fillable = [
        "user_id",
        "package_id",
        "sort_id",
        "word_id",
        "keywords",
    ];

    protected $casts = [
        "keywords" => "array"
    ];

    public function package()
    {
        return $this->belongsTo(Baseklass::class, "package_id");
    }

    public function wordSort()
    {
        return $this->belongsTo(Sort::class, "sort_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
