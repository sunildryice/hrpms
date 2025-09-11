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
        Schema::create('mfr_agreements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('partner_organization_id')->nullable();
            $table->unsignedBigInteger('district_id')->nullable();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->text('grant_number')->nullable();
            $table->date('effective_from');
            $table->date('effective_to');
            $table->decimal('approved_budget', 12, 2)->nullable();
            $table->decimal('opening_balance', 12, 2)->nullable();
            $table->text('opening_remarks')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->default(null);
            $table->unsignedBigInteger('updated_by')->nullable()->default(null);
            $table->nullableTimestamps();

            $table->foreign('partner_organization_id')->references('id')->on('lkup_partner_organizations');
            $table->foreign('district_id')->references('id')->on('lkup_districts');
            $table->foreign('project_id')->references('id')->on('lkup_project_codes');
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mfr_agreements');
    }
};
