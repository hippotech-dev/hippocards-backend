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
        Schema::create('v3_course_certificates', function (Blueprint $table) {
            $table->id();
            $table->date("issue_date");
            $table->unsignedInteger("user_id")->index();
            $table->timestamps();

            $table->foreignId("v3_course_id")
                ->nullable()
                ->references("id")
                ->on("v3_courses")
                ->nullOnDelete();

            $table->foreignId("v3_course_exam_instance_id")
                ->nullable()
                ->references("id")
                ->on("v3_course_exam_instances")
                ->nullOnDelete();

            $table->foreignId("v3_asset_id")
                ->nullable()
                ->references("id")
                ->on("v3_assets")
                ->nullOnDelete();

            $table->foreign("user_id")
                ->nullable()
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_course_certificates');
    }
};
