<?php

namespace App\Models\Article;

use App\Models\Package\Baseklass;
use App\Models\User\User;
use App\Models\Utility\Favorite;
use App\Models\Utility\Language;
use App\Models\Utility\MainCategory;
use App\Models\Utility\Paragraph;
use App\Models\Utility\Question;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';

    protected $fillable = [
        "title",
        "description",
        "language_id",
        "level_id",
        "image",
        "time",
        "is_review",
        "package_id",
        "author",
        "category_id",
        "mp3",
        "is_featured",
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, "author", 'id');
    }

    public function language()
    {
        return $this->belongsTo(Language::class, "language_id", "id");
    }

    public function category()
    {
        return $this->belongsTo(MainCategory::class, "category_id", "id");
    }

    public function baseklass()
    {
        return $this->belongsTo(Baseklass::class, "package_id", "id");
    }

    public function paragraphs()
    {
        return $this->morphMany(Paragraph::class, 'object')->orderBy('order');
    }

    public function timestamp()
    {
        return $this->hasOne(Timestamps::class, 'article_id', 'id');
    }

    public function morphQuestions()
    {
        return $this->morphMany(Question::class, "object", "object_type", "object_id");
    }

    public function favorites()
    {
        return $this->morphMany(Favorite::class, "object", "object_type", "object_id");
    }

    public function package()
    {
        return $this->belongsTo(Baseklass::class, "package_id");
    }

    public function scopeActive($query)
    {
        return $query->where("is_review", false);
    }
}
