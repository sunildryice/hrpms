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
        Schema::create('constructions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('fiscal_year_id')->nullable()->default(null);
            $table->unsignedBigInteger('office_id')->nullable()->default(null);
            $table->unsignedBigInteger('province_id')->nullable()->default(null);
            $table->unsignedBigInteger('district_id')->nullable()->default(null);
            $table->unsignedBigInteger('local_level_id')->nullable()->default(null);
            $table->string('health_facility_name')->nullable()->default(null);
            $table->string('facility_type')->nullable()->default(null);
            $table->string('type_of_work')->nullable()->default(null);
            $table->string('engineer_name')->nullable()->default(null);
            $table->date('signed_date')->nullable()->default(null);
            $table->date('effective_date_from')->nullable()->default(null);
            $table->date('effective_date_to')->nullable()->default(null);
            $table->unsignedTinyInteger('ohw_contribution')->default(0);
            $table->boolean('approval')->default(0);
            $table->decimal('total_contribution_amount', 12, 2)->default(0);
            $table->unsignedTinyInteger('total_contribution_percentage')->default(0);

            $table->longText('donor')->nullable()->default(null);
            $table->longText('metal_plaque_text')->nullable()->default(null);

            $table->unsignedBigInteger('reviewer_id')->nullable()->default(null);
            $table->unsignedBigInteger('approver_id')->nullable()->default(null);
            $table->unsignedBigInteger('status_id')->nullable()->default(null);
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('fiscal_year_id')->references('id')->on('lkup_fiscal_years');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
            $table->foreign('district_id')->references('id')->on('lkup_districts');
            $table->foreign('local_level_id')->references('id')->on('lkup_local_levels');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('reviewer_id')->references('id')->on('users');
            $table->foreign('approver_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('constructions');
    }
};
