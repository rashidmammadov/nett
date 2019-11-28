<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_MATCH_TABLE, function (Blueprint $table) {
            $table->bigIncrements(MATCH_ID);
            $table->integer(TOURNAMENT_ID)->references(TOURNAMENT_ID)->on(DB_TOURNAMENT_TABLE);
            $table->unsignedTinyInteger(TOUR_ID);
            $table->char(TOUR_NAME, 20)->nullable();
            $table->integer(HOME_ID)->nullable()->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->integer(AWAY_ID)->nullable()->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->unsignedSmallInteger(HOME_POINT)->nullable();
            $table->unsignedSmallInteger(AWAY_POINT)->nullable();
            $table->integer(WINNER_ID)->nullable()->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->integer(LOSER_ID)->nullable()->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->string(NOTE, 250)->nullable();
            $table->boolean(AVAILABLE)->default(false);
            $table->char(DATE, 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_MATCH_TABLE);
    }
}
