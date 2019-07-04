<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePqchoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pqchoices', function (Blueprint $table) {
            $table->increments('id');
            $table->string('choice');
            
            $table->integer('paperquestion_id')->unsigned();
            $table->foreign('paperquestion_id')
                  ->references('id')
                  ->on('paperquestions')
                  ->onDelete('cascade');
            
            $table->integer('position');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pqchoices');
    }
}
