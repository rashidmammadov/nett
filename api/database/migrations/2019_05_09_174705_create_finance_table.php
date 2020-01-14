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
            $table->increments(FINANCE_ID);
            $table->char(REFERENCE_CODE, 15)->nullable();
            $table->integer(USER_ID)->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->char(TYPE, 10);
            $table->char(CHANNEL, 20);
            $table->integer(TOURNAMENT_ID)->nullable();
            $table->char(IBAN, 255)->nullable();
            $table->double(AMOUNT)->nullable();
            $table->double(AMOUNT_WITH_COMMISSION)->nullable();
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
