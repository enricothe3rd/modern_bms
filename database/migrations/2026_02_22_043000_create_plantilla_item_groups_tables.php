<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('plantilla_item_groups')) {
            Schema::create('plantilla_item_groups', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('plantilla_item_id');
                $table->string('group_type', 10); // to | from
                $table->string('group_label')->nullable();
                $table->decimal('group_total', 15, 2)->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('plantilla_item_id', 'plantilla_item_groups_item_fk')
                    ->references('id')->on('plantilla_items')->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('plantilla_item_group_movements')) {
            Schema::create('plantilla_item_group_movements', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('plantilla_item_group_id');
                $table->string('label')->nullable();
                $table->unsignedBigInteger('salary_schedule_id')->nullable();
                $table->unsignedTinyInteger('salary_grade')->nullable();
                $table->unsignedTinyInteger('salary_step')->nullable();
                $table->decimal('salary_amount', 15, 2)->nullable();
                $table->decimal('movement_total', 15, 2)->default(0);
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('plantilla_item_group_id', 'plantilla_item_group_movements_group_fk')
                    ->references('id')->on('plantilla_item_groups')->cascadeOnDelete();
                $table->foreign('salary_schedule_id', 'plantilla_item_group_movements_sched_fk')
                    ->references('id')->on('salary_schedules')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('plantilla_item_group_movements');
        Schema::dropIfExists('plantilla_item_groups');
    }
};
