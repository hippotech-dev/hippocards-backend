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
        Schema::create("v3_payment_orders", function (Blueprint $table) {
            $table->id();
            $table->uuid("number")->index();
            $table->unsignedInteger("user_id")->nullable();
            $table->tinyInteger("status")->default(EStatus::PENDING->value);
            $table->string("type")->index();
            $table->integer("total_amount");
            $table->integer("total_items");
            $table->timestamps();

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->nullOnDelete();
        });

        Schema::create("v3_payment_order_items", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->nullable();
            $table->unsignedBigInteger("object_id")->index();
            $table->string("object_type")->index();
            $table->integer("amount");
            $table->timestamps();

            $table
                ->foreign("user_id")
                ->index()
                ->references("id")
                ->on("users")
                ->nullOnDelete();

            $table
                ->foreignId("v3_payment_order_id")
                ->index()
                ->references("id")
                ->on("v3_payment_orders")
                ->cascadeOnDelete();
        });

        Schema::create("v3_payment_invoices", function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger("user_id")->nullable()->index();
            $table->string("identifier")->index();
            $table->string("merchant_payment_id")->nullable();
            $table->string("merchant_invoice_id")->nullable();
            $table->integer("total_amount");
            $table->integer("total_pending_amount")->default(0);
            $table->integer("total_paid_amount")->default(0);
            $table->string("payment_method")->index()->nullable();
            $table->tinyInteger("status")->default(EStatus::PENDING->value);
            $table->string("redirect_uri", 512)->nullable();
            $table->timestamps();

            $table
                ->foreign("user_id")
                ->references("id")
                ->on("users")
                ->nullOnDelete();

            $table
                ->foreignId("v3_payment_order_id")
                ->index()
                ->references("id")
                ->on("v3_payment_orders")
                ->cascadeOnDelete();
        });

        Schema::create("v3_payment_invoice_responses", function (
            Blueprint $table
        ) {
            $table->id();
            $table->string("identifier");
            $table->string("payment_method");
            $table->json("content");
            $table->smallInteger("status_code");
            $table->timestamps();

            $table
                ->foreignId("v3_payment_invoice_id")
                ->index()
                ->references("id")
                ->on("v3_payment_invoices")
                ->cascadeOnDelete();

            $table
                ->foreignId("v3_payment_order_id")
                ->index()
                ->references("id")
                ->on("v3_payment_orders")
                ->cascadeOnDelete();
        });

        Schema::create("v3_access_tokens", function (Blueprint $table) {
            $table->id();
            $table->string("access_token", 2048);
            $table->timestamp("access_expire");
            $table->string("refresh_token", 2048)->nullable();
            $table->timestamp("refresh_expire")->nullable();
            $table->string("type")->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("v3_payment_order_items");
        Schema::dropIfExists("v3_payment_invoice_responses");
        Schema::dropIfExists("v3_payment_invoices");
        Schema::dropIfExists("v3_payment_orders");
        Schema::dropIfExists("v3_access_tokens");
    }
};
