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
        Schema::create("v3_user_web_browsers", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();

            $table->string("device_id")->index();
            $table->text("user_agent");
            $table->integer("screen_width");
            $table->integer("screen_height");
            $table->string("language", 16);

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();

            $table->timestamps();

            $table->unique([ "user_id", "device_id" ]);
        });

        Schema::create("v3_user_sessions", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->string("access_token", 96)->unique();
            $table->timestamp("last_access_at");

            $table->foreignId("v3_web_browser_id")
                ->references("id")
                ->on("v3_user_web_browsers")
                ->cascadeOnDelete();
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();

            $table->timestamps();
        });

        Schema::table("v3_oauth_authentication_attempts", function (Blueprint $table) {
            $table->json("device")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_oauth_authentication_attempts", function (Blueprint $table) {
            $table->dropColumn("device");
        });

        Schema::dropIfExists("v3_user_sessions");
        Schema::dropIfExists("v3_user_web_browsers");
    }
};
