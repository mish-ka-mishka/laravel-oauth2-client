<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOAuth2AccessTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('oauth2_access_tokens', function (Blueprint $table) {
            $table->id();

            $table->string('provider');

            $table->string('tokenable_type')->nullable();
            $table->integer('tokenable_id')->nullable();

            $table->text('access_token');
            $table->text('refresh_token')->nullable();
            $table->string('resource_owner_id')->nullable();
            $table->json('values')->nullable();
            $table->dateTime('expires_at')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('oauth2_access_tokens');
    }
}
