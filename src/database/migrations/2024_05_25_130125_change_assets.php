<?php

use App\Enums\EStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("v3_assets", function (Blueprint $table) {
            $table->string("vdo_drm_video_id")->nullable()->index();
            $table->string("vdo_drm_video_status")->default(EStatus::PENDING);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_assets", function (Blueprint $table) {
            $table->dropColumn("vdo_drm_video_id");
            $table->dropColumn("vdo_drm_video_status");
        });
    }
};
