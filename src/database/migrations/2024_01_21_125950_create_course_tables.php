<?php

use App\Enums\EAssetType;
use App\Enums\ECourseBlockType;
use App\Enums\ECourseBlockVideoTimestampType;
use App\Enums\ECourseBlockVideoType;
use App\Enums\ELanguageLevel;
use App\Enums\EStatus;
use App\Enums\ETestType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists("v3_course_completions");
        Schema::dropIfExists("v3_course_exam_results");
        Schema::dropIfExists("v3_course_video_timestamps");
        Schema::dropIfExists("v3_course_exam_instances");
        Schema::dropIfExists("v3_course_block_videos");
        Schema::dropIfExists("v3_course_packages");
        Schema::dropIfExists("v3_course_group_blocks");
        Schema::dropIfExists("v3_course_groups");
        Schema::dropIfExists("v3_course_pricings");
        Schema::dropIfExists("v3_course_details");
        Schema::dropIfExists("v3_courses");
        Schema::dropIfExists("v3_assets");

        Schema::create("v3_assets", function (Blueprint $table) {
            $table->id();
            $table->string("path", 256);
            $table->integer("size")->default(0);
            $table->enum("type", EAssetType::values())->default(EAssetType::UNKNOWN->value);
            $table->json("metadata")->nullable();
        });

        Schema::create("v3_courses", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->string("description")->nullable();
            $table->string("thumbnail")->nullable();
            $table->enum("level", ELanguageLevel::values())->default(ELanguageLevel::BEGINNER->value);
            $table->json("additional")->nullable();
            $table->foreignId("v3_thumbnail_asset_id")
                ->nullable()
                ->references("id")
                ->on("v3_assets")
                ->nullOnDelete();
            $table->integer("language_id")->index()->nullable();
            $table->unsignedInteger("author_id")->index()->nullable();
            $table->foreign("language_id")
                ->references("id")
                ->on("language")
                ->nullOnDelete();
            $table->foreign("author_id")
                ->references("id")
                ->on("users")
                ->nullOnDelete();
            $table->timestamps();
        });

        Schema::create("v3_course_details", function (Blueprint $table) {
            $table->id();
            $table->json("content")->nullable();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_pricings", function (Blueprint $table) {
            $table->id();
            $table->double("price")->default(0);
            $table->string("price_string")->default("0");
            $table->integer("duration_days");
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_groups", function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->tinyInteger("type")->nullable();
            $table->integer("order")->default(0);
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_group_blocks", function (Blueprint $table) {
            $table->id();
            $table->string("name")->nullable();
            $table->integer("order")->default(0);
            $table->enum("type", ECourseBlockType::values())->default(ECourseBlockType::LESSON->value);
            $table->integer("sort_id")->index()->nullable();
            $table->foreign("sort_id")
                ->references("id")
                ->on("sort")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_group_id")
                ->index()
                ->nullable()
                ->references("id")
                ->on("v3_course_groups")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_packages", function (Blueprint $table) {
            $table->id();
            $table->integer("package_id")->index();
            $table->foreignId("v3_course_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->foreign("package_id")
                ->references("id")
                ->on("baseklass")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_block_videos", function (Blueprint $table) {
            $table->id();
            $table->enum("type", ECourseBlockVideoType::values())->default(ECourseBlockVideoType::TRANSLATION->value);
            $table->integer("duration")->default(0);
            $table->foreignId("v3_course_group_block_id")
                ->index()
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->foreignId("v3_asset_id")
                ->index()
                ->references("id")
                ->on("v3_assets")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_video_timestamps", function (Blueprint $table) {
            $table->id();
            $table->foreignId("v3_course_video_id")
                ->index()
                ->references("id")
                ->on("v3_course_block_videos")
                ->cascadeOnDelete();
            $table->enum("type", ECourseBlockVideoTimestampType::values())->default(ECourseBlockVideoTimestampType::EXAM->value);
            $table->json("content")->nullable();
            $table->integer("start")->default(0);
            $table->integer("end")->default(0);
        });

        Schema::create("v3_course_exam_instances", function (Blueprint $table) {
            $table->id();
            $table->enum("type", ETestType::values());
            $table->json("questions")->nullable();
            $table->foreignId("v3_course_group_block_id")
                ->index()
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create("v3_course_exam_results", function (Blueprint $table) {
            $table->id();
            $table->json("answers", "[]");
            $table->unsignedInteger("user_id")->index();
            $table->foreignId("v3_course_exam_instance_id")
                ->index()
                ->references("id")
                ->on("v3_course_exam_instances")
                ->cascadeOnDelete();
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create("v3_course_completions", function (Blueprint $table) {
            $table->id();
            $table->enum("status", EStatus::values())->default(EStatus::PENDING->value);
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
            $table->foreignId("v3_course_group_block_id")
                ->index()
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("v3_course_completions");
        Schema::dropIfExists("v3_course_exam_results");
        Schema::dropIfExists("v3_course_video_timestamps");
        Schema::dropIfExists("v3_course_exam_instances");
        Schema::dropIfExists("v3_course_block_videos");
        Schema::dropIfExists("v3_course_packages");
        Schema::dropIfExists("v3_course_group_blocks");
        Schema::dropIfExists("v3_course_groups");
        Schema::dropIfExists("v3_course_pricings");
        Schema::dropIfExists("v3_course_details");
        Schema::dropIfExists("v3_courses");

    }
};
