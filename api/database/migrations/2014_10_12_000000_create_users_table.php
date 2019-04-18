<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_USERS_TABLE, function (Blueprint $table) {
            $table->increments(IDENTIFIER);
            $table->string(USERNAME, 30)->unique();
            $table->string(EMAIL, 50)->unique();
            $table->char(TYPE, 10);
            $table->string(PASSWORD, 500);
            $table->string(REMEMBER_TOKEN, 500)->nullable();
            $table->string(NAME, 30)->nullable();
            $table->string(SURNAME, 30)->nullable();
            $table->string(BIRTHDAY, 20)->nullable();
            $table->char(SEX, 10)->nullable();
            $table->string(PHONE, 15)->nullable();
            $table->string(CITY)->nullable();
            $table->string(DISTRICT)->nullable();
            $table->string(ADDRESS)->nullable();
            $table->string(PICTURE)->nullable();
            $table->string(IBAN)->nullable();
            $table->double(BUDGET)->default(0);
            $table->double(PROMOTION)->default(0);
            $table->integer(TICKET)->default(0);
            $table->integer(RANKING)->nullable();
            $table->boolean(STATE)->default(0);
            $table->string(ONESIGNAL_DEVICE_ID, 50)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_USERS_TABLE);
    }
}
