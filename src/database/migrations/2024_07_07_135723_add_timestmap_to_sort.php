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
        Schema::table('sort', function (Blueprint $table) {
            $table->timestamps();
        });

        Schema::table("baseklass", function (Blueprint $table) {
            $table->integer("sort")->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sort', function (Blueprint $table) {
            $table->dropTimestamps();
        });

        Schema::table("baseklass", function (Blueprint $table) {
            $table->dropColumn("sort");
        });
    }
};
