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
        Schema::create('v3_user_package_progresses', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->integer("package_id")->index();
            $table->integer("language_id")->index();
            $table->integer("progress")->default(0);
            $table->integer("package_word_count")->default(0);
            $table->integer("total_exam_count")->default(0);
            $table->integer("total_final_exam_count")->default(0);
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreign("language_id")
                ->references("id")
                ->on("language")
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
        Schema::dropIfExists('v3_user_package_progresses');
    }
};
