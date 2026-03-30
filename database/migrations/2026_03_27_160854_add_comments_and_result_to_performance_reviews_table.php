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
            $table->text('employee_comments')->nullable()->after('approver_id');
            $table->text('reviewer_comments')->nullable()->after('employee_comments');
            $table->text('result')->nullable()->after('reviewer_comments');
            $table->text('comments')->nullable()->after('result');
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
            $table->dropColumn([
                'employee_comments',
                'reviewer_comments',
                'result',
                'comments'
            ]);
        });
    }
};
