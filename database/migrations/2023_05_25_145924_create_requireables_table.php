<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequireablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requireables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requirement_documents_id');
            $table->foreignId('requireable_id');
            $table->string('requireable_type');
            
            $table->string('file_path')->nullable();
            $table->date('date_due')->nullable();
            $table->foreignId('status_id')->nullable();

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
        Schema::dropIfExists('requireables');
    }
}
