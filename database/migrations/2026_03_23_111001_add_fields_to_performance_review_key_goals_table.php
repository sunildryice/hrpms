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
        Schema::table('performance_review_key_goals', function (Blueprint $table) {
            $table->text('output_deliverables')->nullable()->after('title');
            $table->text('major_activities_employee')->nullable()->after('output_deliverables');
            $table->enum('status', ['not_completed', 'partially_completed', 'fully_completed'])->nullable()->after('major_activities_employee');
            $table->text('remarks_employee')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('performance_review_key_goals', function (Blueprint $table) {
            $table->dropColumn([
                'output_deliverables',
                'major_activities_employee',
                'status',
                'remarks_employee',
            ]);
        });
    }
};
