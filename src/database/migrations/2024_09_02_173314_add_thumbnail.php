<?php

use App\Enums\EAssetUploadType;
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
            $table->string("thumbnail_path")->nullable();
            $table->tinyInteger("upload_type")->default(EAssetUploadType::UPLOAD->value);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_assets", function (Blueprint $table) {
            $table->dropColumn("thumbnail_path");
            $table->dropColumn("upload_type");
        });
    }
};
