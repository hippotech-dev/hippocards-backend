<?php

namespace App\Models\Course;

use App\Enums\ECourseBlockType;
use Illuminate\Database\Eloquent\Model;

class CourseExamInstance extends Model
{
    public $table = "v3_course_exam_instances";

    protected $fillable = [
        "type",
        "questions",
        "answers",
        "v3_course_block_id",
        "v3_course_group_id",
        "v3_user_course_id",
        "v3_course_id",
        "user_id",
        "start_time",
        "end_time",
        "current_question_number",
        "total_questions"
    ];

    public $casts = [
        "type" => ECourseBlockType::class,
        "questions" => "array",
        "answers" => "array",
        "start_time" => "datetime",
        "end_time" => "datetime"
    ];

    public function courseBlock()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }

    public function course()
    {
        return $this->belongsTo(CourseGroupBlock::class);
    }
}

[{"body": "<ol><li>Амьдралд хамгийн их хэрэг болдог нэг үгсийг танд заана.</li><li>Үгийн утгыг олон талаас нь \"ингэж хэрэглэдэг юм шүү\" гээд хэлж өгнө.</li><li>Дайвар утга, холбоо үгэнд ордог утга зэргээр нь дэлгэрүүлж заана.</li><li>Манай судалгаагаар хүмүүсийн 20% нь төлөөлөх, өөр төстэй үгтэй холбох маягаар шинэ үгийг сурдаг. Энэ маягаар сурах дуртай юу? Тийм бол энэ сургалт танд бас таалагднаа.</li></ol>", "index": 0, "title": "Та юуг сургалтаар сурах вэЮ"}, {"body": "<ol><li>Та буруу зөрүү ч болов уншиж чаддаг байхад л болно.</li><li>Та компьютер дээр уг сургалтыг хүлээн авбал илүү зохимжтой байх болно.</li></ol>"