<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('v3_course_exam_instances', function (Blueprint $table) {
            $table->foreignId("v3_user_course_id")
                ->nullable()
                ->index()
                ->references("id")
                ->on("v3_user_courses")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
