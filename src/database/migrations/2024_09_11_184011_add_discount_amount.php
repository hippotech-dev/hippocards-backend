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
        Schema::table("v3_payment_orders", function (Blueprint $table) {
            $table->float("total_discount_amount")->default(0);
        });

        Schema::table("v3_payment_invoices", function (Blueprint $table) {
            $table->float("total_discount_amount")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_payment_orders", function (Blueprint $table) {
            $table->dropColumn("total_discount_amount");
        });

        Schema::table("v3_payment_invoices", function (Blueprint $table) {
            $table->dropColumn("total_discount_amount");
        });
    }
};
