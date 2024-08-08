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
        Schema::table("v3_course_details", function (Blueprint $table) {
            $table->string("about_video_path")->nullable();
            $table->foreignId("v3_about_video_asset_id")
                ->nullable()
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
        Schema::table("v3_course_details", function (Blueprint $table) {
            $table->dropForeign([ "v3_about_video_asset_id" ]);
            $table->dropColumn("about_video_path");
        });
    }
};
