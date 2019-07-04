<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperReviewquestionUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_reviewquestion_user', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('paper_id')->unsigned();
            $table->foreign('paper_id')
                  ->references('id')
                  ->on('papers')
                  ->onDelete('cascade');

            $table->integer('reviewquestion_id')->unsigned();
            $table->foreign('reviewquestion_id')
                  ->references('id')
                  ->on('reviewquestions')
                  ->onDelete('cascade');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->integer('rqchoice_id')->unsigned();
            $table->foreign('rqchoice_id')
                  ->references('id')
                  ->on('rqchoices')
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
        Schema::dropIfExists('paper_reviewquestion_user');
    }
}
