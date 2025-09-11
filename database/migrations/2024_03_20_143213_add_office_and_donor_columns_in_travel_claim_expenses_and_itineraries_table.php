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
        Schema::table('travel_claim_expenses', function (Blueprint $table) {
            $table->unsignedBigInteger('donor_code_id')->nullable()->after('activity_code_id');
            $table->unsignedBigInteger('office_id')->nullable()->after('expense_amount');

            $table->foreign('donor_code_id')->references('id')->on('lkup_donor_codes');
            $table->foreign('office_id')->references('id')->on('lkup_offices');
        });

        Schema::table('travel_claim_itineraries', function (Blueprint $table) {
            $table->unsignedBigInteger('office_id')->nullable()->after('total_amount');

            $table->foreign('office_id')->references('id')->on('lkup_offices');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_claim_expenses', function (Blueprint $table) {
            $table->dropForeign('travel_claim_expenses_donor_code_id_foreign');
            $table->dropColumn('donor_code_id');
            $table->dropForeign('travel_claim_expenses_office_id_foreign');
            $table->dropColumn('office_id');
        });

        Schema::table('travel_claim_itineraries', function (Blueprint $table) {
            $table->dropForeign('travel_claim_itineraries_office_id_foreign');
            $table->dropColumn('office_id');
        });
    }
};
