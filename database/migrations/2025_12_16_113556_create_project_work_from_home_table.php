<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('work_from_homes', function (Blueprint $table) {
            if (Schema::hasColumn('work_from_homes', 'project_id')) {
                $table->dropForeign(['project_id']);
                $table->dropColumn('project_id');
            }

            if (Schema::hasColumn('work_from_homes', 'deliverables')) {
                $table->dropColumn('deliverables');
            }
        });

        Schema::create('project_work_from_home', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_from_home_id')
                ->constrained('work_from_homes')
                ->cascadeOnDelete();
            $table->foreignId('project_id')
                ->constrained('lkup_project_codes')
                ->cascadeOnDelete();
            $table->json('deliverables');
            $table->timestamps();

            $table->unique(['work_from_home_id', 'project_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_work_from_home');

        Schema::table('work_from_homes', function (Blueprint $table) {
            if (! Schema::hasColumn('work_from_homes', 'project_id')) {
                $table->unsignedBigInteger('project_id')->nullable();

                $table->foreign('project_id')
                    ->references('id')
                    ->on('lkup_project_codes')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('work_from_homes', 'deliverables')) {
                $table->json('deliverables')->nullable();
            }
        });
    }
};
