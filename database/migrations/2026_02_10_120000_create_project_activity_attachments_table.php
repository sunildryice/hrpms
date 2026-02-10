<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_activity_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_activity_id')
                ->constrained('project_activities')
                ->onDelete('cascade');
            $table->string('title');
            $table->string('file_path');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_activity_attachments');
    }
};
