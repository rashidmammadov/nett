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
        Schema::create('users', function (Blueprint $table) {
            $table->increments(IDENTIFIER);
            $table->string(USERNAME, 30)->unique();
            $table->string(EMAIL, 50)->unique();
            $table->char(TYPE, 10);
            $table->string(PASSWORD, 30);
            $table->string(REMEMBER_TOKEN, 400)->nullable();
            $table->string(NAME, 30);
            $table->string(SURNAME, 30);
            $table->string(BIRTHDAY, 20);
            $table->char(SEX, 10);
            $table->string(PHONE, 15)->nullable();
            $table->string(CITY)->nullable();
            $table->string(DISTRICT)->nullable();
            $table->string(ADDRESS)->nullable();
            $table->string(PICTURE)->nullable();
            $table->string(IBAN)->nullable();
            $table->boolean(STATE)->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
}
