<?php

namespace App\Models\Package;

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

}
