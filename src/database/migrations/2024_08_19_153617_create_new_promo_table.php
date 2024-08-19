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
        Schema::create('v3_promo_codes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("object_id")->nullable();
            $table->string("object_type")->nullable();
            $table->string("code")->index();
            $table->tinyInteger("type");
            $table->integer("amount");
            $table->integer("amount_type");
            $table->tinyInteger("usage_type");
            $table->tinyInteger("context_type");
            $table->integer("total_quantity")->default(9);
            $table->tinyInteger("status");
            $table->string("description")->nullable();
            $table->timestamps();

            $table->index([ "object_id", "object_type" ]);
        });

        Schema::create("v3_promo_usages", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->index();
            $table->unsignedBigInteger("object_id")->nullable();
            $table->string("object_type")->nullable();

            $table->timestamps();

            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreignId("v3_promo_code_id")
                ->index()
                ->references("id")
                ->on("v3_promo_codes")
                ->cascadeOnDelete();
        });

        Schema::table("v3_payment_invoices", function (Blueprint $table) {
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
        Schema::table("v3_payment_invoices", function (Blueprint $table) {
            $table->dropForeign([ "v3_promo_code_id" ]);
        });

        Schema::table("v3_payment_invoices", function (Blueprint $table) {
            $table->dropColumn("v3_promo_code_id");
        });

        Schema::dropIfExists('v3_promo_usages');
        Schema::dropIfExists('v3_promo_codes');
    }
};
