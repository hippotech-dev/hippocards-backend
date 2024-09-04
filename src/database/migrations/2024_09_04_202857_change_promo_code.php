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
        Schema::table("v3_promo_codes", function (Blueprint $table) {
            $table->integer("total_used")->default(0);
        });

        Schema::table("v3_payment_orders", function (Blueprint $table) {
            $table->foreignId("v3_promo_code_id")
                ->nullable()
                ->references("id")
                ->on("v3_promo_codes")
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_promo_codes", function (Blueprint $table) {
            $table->dropColumn("total_used");
        });

        Schema::table("v3_payment_orders", function (Blueprint $table) {
            $table->dropForeign(["v3_promo_code_id"]);
        });

        Schema::table("v3_payment_orders", function (Blueprint $table) {
            $table->dropColumn("v3_promo_code_id");
        });
    }
};
