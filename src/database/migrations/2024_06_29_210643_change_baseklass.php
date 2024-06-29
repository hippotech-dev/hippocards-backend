<?php

use App\Enums\EPackageType;
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
        Schema::table("baseklass", function (Blueprint $table) {
            // $table->dropColumn("lb_level");
            // $table->dropColumn("slide_type_id");
            // $table->dropColumn("corp_id");
            // $table->dropColumn("is_corporate");
            // $table->dropColumn("hid");
            // $table->dropColumn("weekly_new");
            // $table->dropColumn("is_new");
            // $table->dropColumn("review_class");
            // $table->dropColumn("show_et");
            // $table->dropColumn("show_ex");
            // $table->dropColumn("level");
            // $table->dropColumn("exam");
            // $table->dropColumn("is_mobile");
            // $table->dropColumn("unavailable_msg");
            // $table->dropColumn("class_length_by_text");
            // $table->dropColumn("class_length_by_day");
            // $table->dropColumn("color");
            // $table->dropColumn("sort");
            // $table->dropColumn("video");
            // $table->dropColumn("image3");
            // $table->dropColumn("price");
            // $table->dropColumn("is_available");
            // $table->dropColumn("group_name");
            // $table->dropColumn("is_active");
            // $table->dropColumn("offer_level");
            // $table->dropColumn("new_type");
            // $table->dropColumn("is_award");
            // $table->dropColumn("star");
            $table->integer("status")->default(EStatus::PENDING);
            $table->integer("type")->default(EPackageType::DEFAULT)->index();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("baseklass", function (Blueprint $table) {
            $table->dropColumn("status");
            $table->dropColumn("type");
        });
    }
};
