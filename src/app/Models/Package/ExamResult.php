<?php

namespace App\Models\Package;

use App\Models\Package\Word\Word;
use Illuminate\Database\Eloquent\Model;

class ExamResult extends Model
{
    protected $table = 'exam_result';

    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'word_id',
        'question_id',
        'baseklass_id',
        'exam_id',
        'status',
        'type'
    ];

    public function word()
    {
        return $this->belongsTo(Word::class);
    }

    public function package()
    {
        return $this->belongsTo(Baseklass::class, "baseklass_id");
    }
}
