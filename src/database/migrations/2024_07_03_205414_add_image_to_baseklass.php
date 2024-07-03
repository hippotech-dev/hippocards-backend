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
        Schema::table('baseklass', function (Blueprint $table) {
            $table->string("thumbnail_path")->nullable();
            $table->foreignId("v3_thumbnail_asset_id")
                ->nullable()
                ->references("id")
                ->on("v3_assets")
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baseklass', function (Blueprint $table) {
            $table->dropForeign([ "v3_thumbnail_asset_id" ]);
        });

        Schema::table('baseklass', function (Blueprint $table) {
            $table->dropColumn("thumbnail_path");
            $table->dropColumn("v3_thumbnail_asset_id");
        });
    }
};
