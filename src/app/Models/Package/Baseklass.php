<?php

namespace App\Models\Package;

use App\Enums\EPackageType;
use App\Enums\EStatus;
use App\Models\Package\Sort;
use App\Models\User\User;
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
        "main_category_id",
        "icon_id",
        "language_id",
        "for_kids",
        "word_count",
        "sort",
        "article_id",
        "type",
        "status",
        "created_by"
    ];

    protected $casts = [
        "type" => EPackageType::class,
        "status" => EStatus::class
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

    public function createdBy()
    {
        return $this->belongsTo(User::class, "created_by");
    }

    /**
     * Scopes
     */

    public function scopeByType($query, EPackageType $type)
    {
        return $query->where("type", $type);
    }

    public function scopeActive($query)
    {
        return $query->where("status", EStatus::PACKAGE_PUBLISHED);
    }
}
