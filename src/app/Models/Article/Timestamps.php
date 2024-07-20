<?php

namespace App\Models\Article;

use Illuminate\Database\Eloquent\Model;

class Timestamps extends Model
{
    protected $table = 'timestamps';
    protected $fillable = ['time_stamps', 'article_id'];
    
    public function article()
    {
        return $this->hasOne(Article::class, "article_id", "id");
    }

}
