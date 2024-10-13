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
        Schema::create("v3_web_devices", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");

            $table->string("device_id");
            $table->text("user_agent");
            $table->integer("screen_width");
            $table->integer("screen_height");
            $table->string("language", 16);

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::create("v3_user_sessions", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id");
            $table->mediumText("access_token");
            $table->timestamp("last_access_at");

            $table->foreignId("v3_web_device_id")
                ->references("id")
                ->on("v3_web_devices")
                ->cascadeOnDelete();
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("v3_user_sessions");
        Schema::dropIfExists("v3_web_devices");
    }
};
