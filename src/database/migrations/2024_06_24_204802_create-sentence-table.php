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
        Schema::create("v3_sentences", function (Blueprint $table) {
            $table->id();
            $table->integer("language_id")->index()->nullable();
            $table->unsignedBigInteger("object_id")->nullable();
            $table->string("object_type")->nullable();

            $table->string("value", 2048)->nullable();
            $table->string("translation", 2048)->nullable();
            $table->string("pronunciation", 2048)->nullable();
            $table->string("latin", 2048)->nullable();
            $table->integer("order")->default(0);
            $table->integer("type")->index();
            $table->timestamps();

            $table->foreignId("v3_audio_asset_id")
                ->index()
                ->nullable()
                ->references("id")
                ->on("v3_assets")
                ->nullOnDelete();
            $table->foreign("language_id")
                ->references("id")
                ->on("language")
                ->nullOnDelete();

            $table->index([ "object_id", "object_type" ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("v3_sentences");
    }
};
