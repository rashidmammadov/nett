<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParticipantTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_PARTICIPANT_TABLE, function (Blueprint $table) {
            $table->integer(TOURNAMENT_ID)->references(TOURNAMENT_ID)->on(DB_TOURNAMENT_TABLE);
            $table->integer(PARTICIPANT_ID)->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->double(PAYMENT_AMOUNT)->nullable();
            $table->char(PAYMENT_TYPE)->nullable();
            $table->double(EARNINGS)->nullable();
            $table->smallInteger(RANKING)->nullable();
            $table->smallInteger(POINT)->nullable();
            $table->char(REFERENCE_CODE, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_PARTICIPANT_TABLE);
    }
}
