<?php

use App\Enums\ECourseBlockVideoType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('v3_course_block_images', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->foreignId("v3_course_group_block_id")
                ->index()
                ->references("id")
                ->on("v3_course_group_blocks")
                ->cascadeOnDelete();
            $table->string("path")->nullable();
            $table->foreignId("v3_asset_id")
                ->index()
                ->references("id")
                ->on("v3_assets")
                ->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::table("v3_assets", function (Blueprint $table) {
            $table->string("name", 128)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_course_block_images');

        Schema::table("v3_assets", function (Blueprint $table) {
            $table->dropColumn("name");
        });
    }
};
