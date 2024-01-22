<?php

use App\Enums\ECodeChallengeMethod;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Query\Expression;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('v3_oauth_authentication_attempts');
        Schema::dropIfExists('v3_oauth_clients');
        Schema::create('v3_oauth_clients', function (Blueprint $table) {
            $table->id();
            $table->string("name", 128);
            $table->string("client_id", 256);
            $table->string("client_secret", 256);
            $table->boolean("is_active")->default(true);
            $table->timestamps();
        });

        Schema::create("v3_oauth_authentication_attempts", function (Blueprint $table) {
            $table->id();
            $table->string("code", 256);
            $table->string("redirect_uri", 256);
            $table->string("state", 256);
            $table->string("challenge", 512);
            $table->string("challenge_method", 12)->default(ECodeChallengeMethod::PLAIN->value);
            $table->json('scopes')->default(new Expression('(JSON_ARRAY())'));
            $table->unsignedInteger("user_id");
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->cascadeOnDelete();
            $table->foreignId("v3_oauth_client_id")
                ->references("id")
                ->on("v3_oauth_clients")
                ->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v3_oauth_authentication_attempts');
        Schema::dropIfExists('v3_oauth_clients');
    }
};
