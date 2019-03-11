<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFixtureTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_FIXTURE_TABLE, function (Blueprint $table) {
            $table->integer(TOURNAMENT_ID)->unique()->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->text(FIXTURE);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_FIXTURE_TABLE);
    }
}
