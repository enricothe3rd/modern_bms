<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('plantilla_item_movements')) {
            return;
        }

        Schema::create('plantilla_item_movements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plantilla_item_id');
            $table->string('label')->nullable();
            $table->unsignedBigInteger('salary_schedule_from_id')->nullable();
            $table->unsignedBigInteger('salary_schedule_to_id')->nullable();
            $table->unsignedTinyInteger('salary_from_grade')->nullable();
            $table->unsignedTinyInteger('salary_from_step')->nullable();
            $table->unsignedTinyInteger('salary_to_grade')->nullable();
            $table->unsignedTinyInteger('salary_to_step')->nullable();
            $table->decimal('salary_from_amount', 15, 2)->nullable();
            $table->decimal('salary_to_amount', 15, 2)->nullable();
            $table->decimal('movement_total', 15, 2)->default(0);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('plantilla_item_id', 'plantilla_item_movements_item_fk')
                ->references('id')->on('plantilla_items')->cascadeOnDelete();
            $table->foreign('salary_schedule_from_id', 'plantilla_item_movements_from_sched_fk')
                ->references('id')->on('salary_schedules')->nullOnDelete();
            $table->foreign('salary_schedule_to_id', 'plantilla_item_movements_to_sched_fk')
                ->references('id')->on('salary_schedules')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_item_movements');
    }
};
