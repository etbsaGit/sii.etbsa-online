<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGpsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('uploaded_by');

            $table->unsignedInteger('gps_group_id')->nullable();
            $table->unsignedInteger('gps_chip_id')->nullable();

            // $table->string('sim')->nullable()->unique();
            // $table->string('imei')->nullable();
            // $table->double('cost', 12, 2)->default(0);

            $table->enum('currency', ['MXN', 'USD'])->default('MXN');
            $table->double('exchange_rate', 12, 2)->default(1);
            $table->double('amount', 12, 2)->default(0);
            $table->string('invoice')->nullable();
            $table->string('payment_type')->nullable();

            $table->timestamp('activation_date')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('upload_date')->nullable();
            $table->timestamp('renew_date')->nullable();

            $table->text('comment')->nullable();
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
        Schema::dropIfExists('gps');
    }
}
