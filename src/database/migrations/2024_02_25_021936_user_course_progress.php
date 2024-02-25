<?php

use App\Enums\EStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists("v3_course_completions");

        Schema::create("v3_user_courses", function (Blueprint $table) {
            $table->id();
            $table->date("start");
            $table->date("end");
            $table->unsignedInteger("user_id")->index();
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create("v3_course_completions", function (Blueprint $table) {
            $table->id();
            $table->foreignId("v3_user_course_id")
                ->index()
                ->references("id")
                ->on("v3_user_courses")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->foreignId("current_group_id")
                ->references("id")
                ->on("v3_course_groups")
                ->cascadeOnDelete();
            $table->foreignId("current_block_id")
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->integer("progress")->default(0);
            $table->boolean("is_final_exam_finished")->default(false);
            $table->timestamps();
        });

        Schema::create("v3_course_completion_items", function (Blueprint $table) {
            $table->id();
            $table->foreignId("v3_course_completion_id")
                ->index()
                ->references("id")
                ->on("v3_course_completions")
                ->cascadeOnDelete();
            $table->foreignId("v3_user_course_id")
                ->index()
                ->references("id")
                ->on("v3_user_courses")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_group_id")
                ->references("id")
                ->on("v3_course_groups")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_block_id")
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->tinyInteger("status")->default(EStatus::PENDING->value);
            $table->timestamps();
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
