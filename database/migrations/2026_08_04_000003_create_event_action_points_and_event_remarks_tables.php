<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('event_action_points', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->text('action_point')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });

        Schema::create('event_remarks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('event_id');
            $table->text('remark')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events')->cascadeOnDelete();
        });

        DB::table('events')->orderBy('id')->chunkById(500, function ($events) {
            foreach ($events as $event) {
                if ($event->action_points) {
                    DB::table('event_action_points')->insert([
                        'event_id'    => $event->id,
                        'action_point' => $event->action_points,
                        'created_at'  => $event->created_at ?? now(),
                        'updated_at'  => $event->updated_at ?? now(),
                    ]);
                }

                if ($event->remarks) {
                    DB::table('event_remarks')->insert([
                        'event_id'   => $event->id,
                        'remark'     => $event->remarks,
                        'created_at' => $event->created_at ?? now(),
                        'updated_at' => $event->updated_at ?? now(),
                    ]);
                }
            }
        });

        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn(['action_points', 'remarks']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('action_points')->nullable();
            $table->text('remarks')->nullable();
        });

        DB::table('event_action_points')->orderBy('id')->chunkById(500, function ($points) {
            foreach ($points as $point) {
                DB::table('events')->where('id', $point->event_id)
                    ->where(function ($query) {
                        $query->whereNull('action_points')->orWhere('action_points', '');
                    })
                    ->update(['action_points' => $point->action_point]);
            }
        });

        DB::table('event_remarks')->orderBy('id')->chunkById(500, function ($remarks) {
            foreach ($remarks as $remark) {
                DB::table('events')->where('id', $remark->event_id)
                    ->where(function ($query) {
                        $query->whereNull('remarks')->orWhere('remarks', '');
                    })
                    ->update(['remarks' => $remark->remark]);
            }
        });

        Schema::dropIfExists('event_remarks');
        Schema::dropIfExists('event_action_points');
    }
};
