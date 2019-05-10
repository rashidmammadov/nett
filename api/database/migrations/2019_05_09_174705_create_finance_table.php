<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFinanceTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_FINANCE_TABLE, function (Blueprint $table) {
            $table->integer(USER_ID)->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->char(TYPE, 10);
            $table->char(CHANNEL, 20);
            $table->integer(TOURNAMENT_ID)->nullable();
            $table->double(AMOUNT)->nullable();
            $table->integer(TICKET)->nullable();
            $table->integer(STATUS)->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_FINANCE_TABLE);
    }
}
