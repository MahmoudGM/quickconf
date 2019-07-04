<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('conferences', function (Blueprint $table) {
            $table->increments('id');
            $table->string('confAcronym');
            $table->string('confName');
            $table->integer('confEdition');
            $table->string('country');
            $table->string('city');
            $table->string('confAdress');    
            $table->string('confUrl');
            $table->string('confMail');
            $table->string('chairMail');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('organizer');
            $table->string('organizerMail');
            $table->string('organizerWebPage')->nullable();;
            $table->string('phone');
            $table->string('researchArea');
            $table->text('confDesc');
            $table->char('camReady',1)->default('N');
            $table->char('blind_review',1)->default('Y');
            $table->char('extended_submission_form',1)->default('Y');
            $table->date('submission_deadline')->nullable();
            $table->date('review_deadline')->nullable();
            $table->date('cam_ready_deadline')->nullable();
            $table->char('is_submission_open',1)->default('Y');
            $table->char('is_cam_ready_open',1)->default('Y');
            $table->integer('discussion_mode')->default(1);
            $table->integer('ballot_mode')->default(1);
            $table->string('upload_dir')->default('FILES');
            $table->integer('nb_reviewer_per_item')->default(2);
            $table->integer('mail_on_upload')->default(1);
            $table->integer('mail_on_review')->default(1);
            $table->string('date_format')->default('F, d, Y');
            $table->string('file_type')->default('pdf,zip,doc,docx');
            $table->boolean('is_activated')->default(0);
            $table->boolean('is_deleted')->nullable();
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
        Schema::dropIfExists('conferences');
    }
}
