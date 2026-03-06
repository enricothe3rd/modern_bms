<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('salary_schedules')) {
            Schema::create('salary_schedules', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->date('effective_date');
                $table->text('notes')->nullable();
                $table->unsignedTinyInteger('total_steps')->default(8);
                $table->unsignedTinyInteger('max_grade')->default(33);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('salary_schedule_cells')) {
            Schema::create('salary_schedule_cells', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('salary_schedule_id');
                $table->unsignedTinyInteger('salary_grade');
                $table->unsignedTinyInteger('step_no');
                $table->decimal('amount', 15, 2)->nullable();
                $table->timestamps();

                $table->foreign('salary_schedule_id', 'salary_sched_cells_sched_fk')
                    ->references('id')
                    ->on('salary_schedules')
                    ->cascadeOnDelete();

                $table->unique(['salary_schedule_id', 'salary_grade', 'step_no'], 'salary_sched_cells_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_schedule_cells');
        Schema::dropIfExists('salary_schedules');
    }
};
