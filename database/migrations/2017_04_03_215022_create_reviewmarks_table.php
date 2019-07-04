<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReviewmarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reviewmarks', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');

            $table->integer('paper_id')->unsigned();
            $table->foreign('paper_id')
                  ->references('id')
                  ->on('papers')
                  ->onDelete('cascade');
            
            $table->integer('criteria_id')->unsigned();
            $table->foreign('criteria_id')
                  ->references('id')
                  ->on('criterias')
                  ->onDelete('cascade');

            $table->integer('mark');

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
        Schema::dropIfExists('reviewmarks');
    }
}
