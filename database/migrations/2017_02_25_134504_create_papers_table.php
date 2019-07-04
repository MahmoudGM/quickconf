<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePapersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('papers', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('conference_id')->unsigned();
            $table->foreign('conference_id')
                  ->references('id')
                  ->on('conferences')
                  ->onDelete('cascade');

            $table->text('title');
            $table->text('abstract');
            $table->text('keywords');
            $table->string('authors')->nullable();

            $table->integer('paperstatus_id')->unsigned()->nullable();
            $table->foreign('paperstatus_id')
                  ->references('id')
                  ->on('paperstatuses')
                  ->onDelete('cascade');

            $table->integer('session_id')->unsigned()->nullable();
            $table->foreign('session_id')
                  ->references('id')
                  ->on('sessions')
                  ->onDelete('cascade');

            $table->integer('pos_in_session');
            $table->integer('is_uploaded');
            $table->integer('id_conf_session');
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
        Schema::dropIfExists('papers');
    }
}
