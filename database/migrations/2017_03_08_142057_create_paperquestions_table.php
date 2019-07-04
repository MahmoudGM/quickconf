<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperquestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paperquestions', function (Blueprint $table) {
            $table->increments('id');
            
            $table->integer('conference_id')->unsigned();
            $table->foreign('conference_id')
                  ->references('id')
                  ->on('conferences')
                  ->onDelete('cascade');

            $table->string('question');
            $table->integer('required');
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
        Schema::dropIfExists('paperquestions');
    }
}
