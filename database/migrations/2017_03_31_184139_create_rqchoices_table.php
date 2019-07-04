<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRqchoicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rqchoices', function (Blueprint $table) {
            $table->increments('id');

            $table->string('choice');
            
            $table->integer('reviewquestion_id')->unsigned();
            $table->foreign('reviewquestion_id')
                  ->references('id')
                  ->on('reviewquestions')
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
        Schema::dropIfExists('rqchoices');
    }
}
