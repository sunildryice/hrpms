<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('construction_progress_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('construction_progress_id');
            $table->string('title')->nullable();
            $table->string('attachment')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->timestamps();
            
            $table->foreign('construction_progress_id', 'construction_progress_id_foreign')->references('id')->on('construction_progress');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('construction_progress_attachments');
    }
};
