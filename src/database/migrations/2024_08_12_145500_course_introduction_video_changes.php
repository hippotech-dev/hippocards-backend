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
        Schema::table("v3_course_completions", function (Blueprint $table) {
            $table->boolean("is_introduction_completed")->default(false);
        });

        Schema::create("v3_course_introductions", function (Blueprint $table) {
            $table->id();
            $table->json("content");
            $table->string("video_asset_path")->nullable();
            $table->timestamps();

            $table->foreignId("v3_course_id")
                ->references("id")
                ->on("v3_courses")
                ->cascadeOnDelete();
            $table->foreignId("v3_video_asset_id")
                ->nullable()
                ->references("id")
                ->on("v3_assets")
                ->nullOnDelete();
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
