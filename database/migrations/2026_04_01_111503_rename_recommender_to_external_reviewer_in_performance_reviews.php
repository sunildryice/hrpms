<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->renameColumn('recommender_id', 'external_reviewer_id');
            $table->renameColumn('reviewer_comments', 'external_reviewer_comments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_reviews', function (Blueprint $table) {
            $table->renameColumn('external_reviewer_id', 'recommender_id');
            $table->renameColumn('external_reviewer_comments', 'reviewer_comments');
        });
    }
};
