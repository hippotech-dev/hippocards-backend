<?php

namespace App\Models\Package;

use App\Models\Package\Sort;
use App\Models\Utility\Language;
use App\Models\Utility\MainCategory;
use App\Models\Utility\SystemIcon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Baseklass extends Model
{
    use SoftDeletes;

    protected $table = 'baseklass';
    public $timestamps = false;

    protected $fillable = [
      "name",
      "foreign_name",
      "description",
      "group_name",
      "main_category_id",
      "icon_id",
      "is_active",
      "prepare_see",
      "language_id",
      "for_kids",
      "sort",
    ];

    /**
     * Relations
     */

    public function mainCategory()
    {
        return $this->belongsTo(MainCategory::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class, "language_id");
    }

    public function wordSorts()
    {
        return $this->hasMany(Sort::class);
    }

    public function systemIcon()
    {
        return $this->belongsTo(SystemIcon::class);
    }

    /**
     * Scopes
     */

    public function scopeActive($query)
    {
        return $query
            ->where("article_id", false)
            ->where("prepare_see", false);
    }
}
