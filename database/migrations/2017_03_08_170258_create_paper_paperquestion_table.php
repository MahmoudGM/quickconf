<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperPaperquestionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_paperquestion', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('paper_id')->unsigned();
            $table->foreign('paper_id')
                  ->references('id')
                  ->on('papers')
                  ->onDelete('cascade');

            $table->integer('paperquestion_id')->unsigned();
            $table->foreign('paperquestion_id')
                  ->references('id')
                  ->on('paperquestions')
                  ->onDelete('cascade');

            $table->integer('pqchoice_id')->unsigned();
            $table->foreign('pqchoice_id')
                  ->references('id')
                  ->on('pqchoices')
                  ->onDelete('cascade');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paper_paperquestion');
    }
}
