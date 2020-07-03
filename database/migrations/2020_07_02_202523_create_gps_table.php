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
            $table->string('name');
            $table->unsignedInteger('uploaded_by');
            $table->unsignedInteger('gps_group_id');
            $table->string('sim')->nullable()->unique();
            $table->string('imei')->nullable();
            $table->double('cost',12,2)->default(0);
            $table->double('amount',12,2)->default(0);
            $table->timestamp('activation_date')->nullable();
            $table->timestamp('due_date')->nullable();
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
