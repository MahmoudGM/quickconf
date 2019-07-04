<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperTopicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_topic', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('paper_id')->unsigned();
            $table->foreign('paper_id')
                  ->references('id')
                  ->on('papers')
                  ->onDelete('cascade');

            $table->integer('topic_id')->unsigned();
            $table->foreign('topic_id')
                  ->references('id')
                  ->on('topics')
                  ->onDelete('cascade');
                  
            $table->string('main')->default('N');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paper_topic');
    }
}
