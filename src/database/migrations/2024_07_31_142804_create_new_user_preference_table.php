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
        Schema::create('v3_user_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();

            $table->string("type");
            $table->json("value")->nullable();

            $table->timestamps();

            $table->foreign("user_id")
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
        Schema::dropIfExists('v3_user_preferences');
    }
};
