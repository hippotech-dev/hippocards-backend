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
        Schema::table("v3_user_web_browsers", function (Blueprint $table) {
            $table->string("origin")->nullable();
            $table->string("timezone", 32)->nullable();
        });

        Schema::table("v3_user_sessions", function (Blueprint $table) {
            $table->string("origin")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_user_web_browsers", function (Blueprint $table) {
            $table->dropColumn("origin");
            $table->dropColumn("platform");
            $table->dropColumn("timezone");
        });

        Schema::table("v3_user_sessions", function (Blueprint $table) {
            $table->dropColumn("origin");
        });
    }
};
