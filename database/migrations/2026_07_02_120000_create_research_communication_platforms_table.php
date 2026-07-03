<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_communication_platforms', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('research_communication_id');
            $table->string('platform');
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('link_clicks')->default(0);
            $table->unsignedInteger('reactions')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();

            $table->foreign('research_communication_id', 'fk_rcp_research_comm')->references('id')->on('research_communication');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('research_communication_platforms');
    }
};
