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
        Schema::table('off_day_works', function (Blueprint $table) {
            if (Schema::hasColumn('off_day_works', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }

            if (Schema::hasColumn('off_day_works', 'deliverables')) {
                $table->dropColumn('deliverables');
            }
        });

        Schema::create('project_off_day_work', function (Blueprint $table) {
            $table->id();

            $table->foreignId('off_day_work_id')
                ->constrained('off_day_works')
                ->cascadeOnDelete();

            $table->foreignId('project_id')
                ->constrained('lkup_project_codes')
                ->cascadeOnDelete();

            $table->json('deliverables');
            $table->timestamps();

            $table->unique(['off_day_work_id', 'project_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_off_day_work');

        Schema::table('off_day_works', function (Blueprint $table) {
            if (! Schema::hasColumn('off_day_works', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable();

                $table->foreign('project_id')
                    ->references('id')
                    ->on('lkup_project_codes')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('off_day_works', 'deliverables')) {
                $table->json('deliverables')->nullable();
            }
        });
    }
};
