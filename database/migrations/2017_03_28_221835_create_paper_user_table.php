<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaperUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paper_user', function (Blueprint $table) {
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

            $table->integer('ratelabel_id')->unsigned()->nullable();
            $table->foreign('ratelabel_id')
                  ->references('id')
                  ->on('ratelabels')
                  ->onDelete('cascade');
            
            $table->boolean('is_reviewed')->default(0);

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
        Schema::dropIfExists('paper_user');
    }
}
