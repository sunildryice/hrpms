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
        DB::statement("DROP VIEW IF EXISTS view_travel_requests");
        DB::statement("
            CREATE VIEW view_travel_requests AS
                SELECT t1.id, t1.prefix, t1.travel_number, t1.modification_number, t1.departure_date,
                            t1.return_date, t1.final_destination, t1.status_id, s1.title as status_title, s1.status_class,
                            f1.title as fiscal_year, u1.full_name as requester_name, u2.full_name as approver_name, e1.full_name as employee_name,
                            t1.requester_id, t1.employee_id, t1.office_id, t1.approver_id,
                            (SELECT COUNT(*)
                            FROM travel_request_day_itineraries tri
                            WHERE tri.travel_request_id = t1.id
                            AND tri.air_ticket = 1) AS air_ticket_count,
                            (SELECT COUNT(*)
                            FROM travel_request_day_itineraries tri
                            WHERE tri.travel_request_id = t1.id
                            AND tri.air_ticket = 1) AS vehicle_count
                FROM travel_requests t1
                JOIN users u1 ON t1.created_by=u1.id
                JOIN users u2 ON t1.approver_id=u2.id
                JOIN lkup_fiscal_years f1 ON t1.fiscal_year_id=f1.id
                JOIN lkup_status as s1 ON t1.status_id=s1.id
                LEFT JOIN employees as e1 ON t1.employee_id=e1.id
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_travel_requests");
    }
};
