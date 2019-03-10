<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTournamentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    //TOURNAMENT_ID, HOLDER_ID, GAME_ID, FIXTURE_ID, TOURNAMENT_TYPE, PARTICIPANT_COUNT, START_DATE, STATUS
    public function up() {
        Schema::create(DB_TOURNAMENT_TABLE, function (Blueprint $table) {
            $table->increments(TOURNAMENT_ID);
            $table->integer(HOLDER_ID)->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->integer(GAME_ID)->references(GAME_ID)->on(DB_GAME_TABLE);
            $table->string(FIXTURE_ID)->nullable();
            $table->char(TOURNAMENT_TYPE, 20);
            $table->integer(PARTICIPANT_COUNT);
            $table->string(START_DATE,20);
            $table->integer(STATUS)->default(2);
            $table->integer(DAYS)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_TOURNAMENT_TABLE);
    }
}
