<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAgencyBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_bank_accounts', function (Blueprint $table) {
            $table->id();

            $table->foreignId('agency_id')->references('id')->on('agencies');
            $table->string('bank_name');
            $table->string('account_number')->nullable();
            $table->double('balance','12,5')->default(0);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agency_bank_accounts');
    }
}
