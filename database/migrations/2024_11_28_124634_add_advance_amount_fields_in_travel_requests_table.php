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
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->after('remarks', function(Blueprint $table){
                $table->decimal('requested_advance_amount', 10, 2)->default(0);
                $table->date('advance_requested_at')->nullable();
                $table->decimal('received_advance_amount', 10, 2)->default(0);
                $table->date('advance_received_at')->nullable();
                $table->unsignedBigInteger('finance_user_id')->nullable();
                $table->text('requester_advance_remarks')->nullable();
                $table->text('finance_remarks')->nullable();
            });

            $table->foreign('finance_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('travel_requests', function (Blueprint $table) {
            $table->dropForeign('travel_requests_finance_user_id_foreign');
            $table->dropColumn(['requested_advance_amount', 'received_advance_amount', 'advance_received_at','advance_requested_at',  'finance_user_id', 'finance_remarks']);
        });
    }
};
