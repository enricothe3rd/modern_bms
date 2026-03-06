<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plantilla_items')) {
            return;
        }

        Schema::table('plantilla_items', function (Blueprint $table) {
            if (!Schema::hasColumn('plantilla_items', 'salary_schedule_from_id')) {
                $table->unsignedBigInteger('salary_schedule_from_id')->nullable()->after('salary_schedule_id');
            }
            if (!Schema::hasColumn('plantilla_items', 'salary_schedule_to_id')) {
                $table->unsignedBigInteger('salary_schedule_to_id')->nullable()->after('salary_schedule_from_id');
            }
        });

        Schema::table('plantilla_items', function (Blueprint $table) {
            try {
                $table->foreign('salary_schedule_from_id', 'plantilla_items_sched_from_fk')
                    ->references('id')->on('salary_schedules')->nullOnDelete();
            } catch (\Throwable $e) {
            }
            try {
                $table->foreign('salary_schedule_to_id', 'plantilla_items_sched_to_fk')
                    ->references('id')->on('salary_schedules')->nullOnDelete();
            } catch (\Throwable $e) {
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('plantilla_items')) {
            return;
        }

        Schema::table('plantilla_items', function (Blueprint $table) {
            try { $table->dropForeign('plantilla_items_sched_from_fk'); } catch (\Throwable $e) {}
            try { $table->dropForeign('plantilla_items_sched_to_fk'); } catch (\Throwable $e) {}
        });

        Schema::table('plantilla_items', function (Blueprint $table) {
            if (Schema::hasColumn('plantilla_items', 'salary_schedule_from_id')) {
                $table->dropColumn('salary_schedule_from_id');
            }
            if (Schema::hasColumn('plantilla_items', 'salary_schedule_to_id')) {
                $table->dropColumn('salary_schedule_to_id');
            }
        });
    }
};
