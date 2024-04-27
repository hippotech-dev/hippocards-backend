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
        Schema::dropIfExists("v3_course_block_details");
        Schema::dropIfExists("v3_course_block_responses");

        Schema::create("v3_course_block_details", function (Blueprint $table) {
            $table->id();
            $table->json("sentences")->nullable();
            $table->json("keywords")->nullable();
            $table->timestamps();

            $table->foreignId("v3_course_id")
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_block_id")
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
        });

        Schema::create("v3_course_block_responses", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->string("sentence", 512)->nullable();
            $table->string("sentence_translation", 512)->nullable();
            $table->string("sentence_hint", 512)->nullable();
            $table->string("keyword")->nullable();
            $table->string("type")->index();
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
            $table->foreignId("v3_course_block_id")
                ->index()
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->foreignId("v3_course_completion_id")
                ->index()
                ->references("id")
                ->on("v3_course_completions")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("v3_course_block_details");
        Schema::dropIfExists("v3_course_block_responses");
    }
};
