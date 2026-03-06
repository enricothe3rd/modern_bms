<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('statement_of_statutory_obligations')) {
            Schema::create('statement_of_statutory_obligations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('statement_of_statutory_obligation_categories')) {
            Schema::create('statement_of_statutory_obligation_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('statement_of_statutory_obligation_id');
                $table->string('category_number', 50);
                $table->string('category_name');
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('statement_of_statutory_obligation_id', 'sso_categories_sso_id_fk')
                    ->references('id')
                    ->on('statement_of_statutory_obligations')
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('statement_of_statutory_obligation_items')) {
            Schema::create('statement_of_statutory_obligation_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('statement_of_statutory_obligation_category_id');
                $table->string('code')->nullable();
                $table->text('description');
                $table->decimal('amount', 15, 2)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('statement_of_statutory_obligation_category_id', 'sso_items_category_id_fk')
                    ->references('id')
                    ->on('statement_of_statutory_obligation_categories')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('statement_of_statutory_obligation_items');
        Schema::dropIfExists('statement_of_statutory_obligation_categories');
        Schema::dropIfExists('statement_of_statutory_obligations');
    }
};
