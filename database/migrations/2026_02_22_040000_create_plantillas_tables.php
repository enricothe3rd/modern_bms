<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plantillas')) {
            Schema::create('plantillas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiscal_year_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('department_id')->constrained()->cascadeOnDelete();
                $table->string('group_name');
                $table->text('notes')->nullable();
                $table->decimal('grand_total', 15, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('plantilla_items')) {
            Schema::create('plantilla_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('plantilla_id');
                $table->string('employee_name');
                $table->string('position');
                $table->unsignedInteger('old_count')->default(0);
                $table->unsignedInteger('new_count')->default(0);
                $table->unsignedBigInteger('salary_schedule_id')->nullable();
                $table->unsignedTinyInteger('salary_from_grade')->nullable();
                $table->unsignedTinyInteger('salary_from_step')->nullable();
                $table->unsignedTinyInteger('salary_to_grade')->nullable();
                $table->unsignedTinyInteger('salary_to_step')->nullable();
                $table->decimal('salary_from_amount', 15, 2)->nullable();
                $table->decimal('salary_to_amount', 15, 2)->nullable();
                $table->decimal('line_total', 15, 2)->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('plantilla_id', 'plantilla_items_plantilla_fk')
                    ->references('id')->on('plantillas')->cascadeOnDelete();
                $table->foreign('salary_schedule_id', 'plantilla_items_sched_fk')
                    ->references('id')->on('salary_schedules')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_items');
        Schema::dropIfExists('plantillas');
    }
};
