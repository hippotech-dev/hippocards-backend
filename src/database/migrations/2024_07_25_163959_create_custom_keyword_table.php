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
        Schema::create('v3_user_custom_word_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->integer("package_id")->index();
            $table->integer("sort_id")->index();
            $table->unsignedInteger("word_id")->index();
            $table->json("keywords")->nullable();
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreign("sort_id")
                ->references("id")
                ->on("sort")
                ->cascadeOnDelete();
            $table->foreign("package_id")
                ->references("id")
                ->on("baseklass")
                ->cascadeOnDelete();
            $table->foreign("word_id")
                ->references("id")
                ->on("word")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_user_custom_word_details');
    }
};
