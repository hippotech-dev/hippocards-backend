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
            $table->unsignedInteger("created_by")->index()->nullable();
            $table->timestamps();

            $table->foreign("created_by")
                ->references("id")
                ->on("users")
                ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('baseklass', function (Blueprint $table) {
            $table->dropForeign([ "created_by" ]);
        });

        Schema::table('baseklass', function (Blueprint $table) {
            $table->dropColumn("created_by");
            $table->dropTimestamps();
        });
    }
};
