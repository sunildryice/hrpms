<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('research_communication', function (Blueprint $table) {
            $table->id();
            // Publication fields
            $table->enum('type_of_publication', ['Journal', 'Other'])->nullable();
            $table->string('publication_title')->nullable();
            $table->date('date_of_publication')->nullable();
            $table->string('herdi_members_involved')->nullable();
            $table->string('journal_paper_name')->nullable();
            $table->string('publication_url')->nullable();

            // Social media post fields
            $table->string('type_of_post')->nullable();
            $table->date('date_posted')->nullable();
            $table->string('post_title')->nullable();
            $table->string('associated_project')->nullable();
            $table->enum('posted_in', ['Bluesky','Facebook','LinkedIn', 'Twitter', 'Website', 'X', 'Youtube'])->nullable();
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('link_clicks')->default(0);
            $table->unsignedInteger('reactions')->default(0);
            $table->unsignedInteger('shares')->default(0);
            $table->unsignedInteger('comments')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->nullableTimestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('research_communication');
    }
};
