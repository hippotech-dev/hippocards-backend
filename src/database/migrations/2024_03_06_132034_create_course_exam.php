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
        Schema::dropIfExists("v3_course_exam_results");
        Schema::dropIfExists("v3_course_exam_instances");

        Schema::create('v3_course_exam_instances', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->string("type");
            $table->json("questions")->nullable();
            $table->json("answers")->nullable();
            $table->integer("total_questions")->default(0);
            $table->integer("current_question_number")->default(0);
            $table->timestamp("start_time")->nullable();
            $table->timestamp("end_time")->nullable();
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_group_id")
                ->nullable()
                ->references("id")
                ->on("v3_course_groups")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_block_id")
                ->nullable()
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_exam_results", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->string("type");
            $table->integer("total_points")->default(0);
            $table->integer("total_received_points")->default(0);
            $table->integer("status")->default(EStatus::PENDING);

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_group_id")
                ->nullable()
                ->references("id")
                ->on("v3_course_groups")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_block_id")
                ->nullable()
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_exam_instance_id")
                ->nullable()
                ->references("id")
                ->on("v3_course_exam_instances")
                ->cascadeOnDelete();
        });

        Schema::table("v3_course_group_blocks", function (Blueprint $table) {
            $table->unsignedInteger("word_id")->nullable();
            $table->integer("package_id")->nullable();

            $table->foreign("word_id")
                ->references("id")
                ->on("word")
                ->cascadeOnDelete();

            $table->foreign("package_id")
                ->references("id")
                ->on("baseklass")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("v3_course_exam_results");
        Schema::dropIfExists("v3_course_exam_instances");

        Schema::table("v3_course_group_blocks", function (Blueprint $table) {
            $table->dropForeign([ "word_id" ]);
            $table->dropForeign([ "package_id" ]);
        });

        Schema::table("v3_course_group_blocks", function (Blueprint $table) {
            $table->dropColumn("word_id");
            $table->dropColumn("package_id");
        });
    }
};
