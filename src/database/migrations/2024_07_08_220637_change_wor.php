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
        Schema::table("word", function (Blueprint $table) {
            $table->integer("update_type")->nullable()->change();
            $table->integer("sort2")->nullable()->change();
            $table->string("mp3")->nullable()->change();
            $table->string("sound")->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("word", function (Blueprint $table) {
            $table->integer("update_type")->nullable(false)->change();
            $table->integer("sort2")->nullable(false)->change();
            $table->string("mp3")->nullable(false)->change();
            $table->string("sound")->nullable(false)->change();
        });
    }
};
