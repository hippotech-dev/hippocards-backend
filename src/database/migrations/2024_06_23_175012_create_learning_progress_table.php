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
        Schema::create('v3_user_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table->unsignedBigInteger("object_id");
            $table->string("object_type");

            $table->integer("action")->index();
            $table->integer("type")->index();
            $table->json("metadata")->nullable();
            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();

            $table->index([ "object_id", "object_type" ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_user_activities');
    }
};
