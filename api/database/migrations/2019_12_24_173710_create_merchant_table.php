<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMerchantTable extends Migration {

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(DB_MERCHANT_TABLE, function (Blueprint $table) {
            $table->integer(MERCHANT_ID)->unique()->references(IDENTIFIER)->on(DB_USERS_TABLE);
            $table->string(MERCHANT_TYPE, 50);
            $table->string(MERCHANT_KEY, 50)->nullable();
            $table->string(IDENTITY_NUMBER, 11);
            $table->string(TAX_OFFICE, 100)->nullable();
            $table->string(TAX_NUMBER, 11)->nullable();
            $table->string(COMPANY_TITLE, 500)->nullable();
            $table->string(IBAN, 30)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists(DB_MERCHANT_TABLE);
    }
}
