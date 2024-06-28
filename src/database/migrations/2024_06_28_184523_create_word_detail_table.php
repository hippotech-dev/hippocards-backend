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
        Schema::create('v3_word_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("word_id");
            $table->string("translation", 512)->nullable();
            $table->string("keyword", 512)->nullable();
            $table->string("pronunciation", 512)->nullable();
            $table->string("hiragana", 512)->nullable();
            $table->integer("part_of_speech")->nullable();
            $table->timestamps();

            $table->foreign("word_id")
                ->references("id")
                ->on("word")
                ->cascadeOnDelete();
        });

        Schema::create("v3_related_words", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("word_id")->index();
            $table->unsignedInteger("related_word_id")->index();
            $table->string("value", 512);
            $table->string("translation", 512)->nullable();
            $table->integer("type");

            $table->timestamps();

            $table->foreign("word_id")
                ->references("id")
                ->on("word")
                ->cascadeOnDelete();
            $table->foreign("related_word_id")
                ->references("id")
                ->on("word")
                ->cascadeOnDelete();
        });

        Schema::create("v3_word_images", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("word_id")->index();
            $table->string("name");
            $table->string("path");
            $table->string("type");

            $table->timestamps();

            $table->foreign("word_id")
                ->references("id")
                ->on("word")
                ->cascadeOnDelete();
            $table->foreignId("v3_asset_id")
                ->references("id")
                ->on("v3_assets")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_word_details');
        Schema::dropIfExists('v3_related_words');
        Schema::dropIfExists('v3_word_images');
    }
};
