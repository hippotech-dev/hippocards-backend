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
        Schema::table("v3_payment_invoices", function (Blueprint $table) {
            $table->string("v3_promo_code_value")->nullable()->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("v3_payment_invoices", function (Blueprint $table) {
            $table->dropColumn("v3_promo_code_value");
        });
    }
};
