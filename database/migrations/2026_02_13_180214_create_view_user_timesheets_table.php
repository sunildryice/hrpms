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
        DB::statement("DROP VIEW IF EXISTS view_user_timesheets");
        DB::statement("
        CREATE VIEW view_user_timesheets AS
        SELECT
            t.id,
            t.year,
            t.start_date,
            t.end_date,
            t.month_name,
            t.requester_id,
            t.approver_id,
            t.status_id,
            IFNULL(SUM(a.hours_spent), 0) AS total_worked_hours,
            GROUP_CONCAT(DISTINCT p.short_name ORDER BY p.short_name SEPARATOR ', ') AS project_short_names
        FROM
            timesheets t
        LEFT JOIN
            project_activity_timesheet a
            ON t.requester_id = a.created_by
            AND a.timesheet_date BETWEEN t.start_date AND t.end_date
        LEFT JOIN
            projects p
            ON a.project_id = p.id
        GROUP BY
            t.id, t.year, t.start_date, t.end_date, t.month_name, t.requester_id,t.approver_id, t.status_id;
    ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("DROP VIEW IF EXISTS view_user_timesheets");
    }
};
