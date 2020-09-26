<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->text('permissions')->nullable();
            $table->string('job_title')->nullable();
            $table->timestamp('last_login')->nullable();
            $table->timestamp('active')->nullable();
            $table->string('activation_key', 255)->nullable();
            $table->string('seller_key')->nullable()->unique();
            $table->foreignId('agency_id')->nullable();
            $table->foreignId('departments_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {

    }

}
