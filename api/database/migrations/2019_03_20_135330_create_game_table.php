<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_GAME_TABLE, function (Blueprint $table) {
            $table->increments(GAME_ID);
            $table->string(GAME_NAME, 100);
            $table->string(GAME_IMAGE, 200)->nullable();
            $table->string(PLAYING_TYPE, 200);
            $table->string(PLATFORMS, 200)->nullable();
            $table->string(DEVELOPER, 100)->nullable();
            $table->string(GAME_TYPE, 100)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_GAME_TABLE);
    }
}
