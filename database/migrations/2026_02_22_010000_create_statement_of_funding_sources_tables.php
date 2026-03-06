<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('statement_of_funding_sources')) {
            Schema::create('statement_of_funding_sources', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
                $table->string('title')->nullable();
                $table->text('remarks')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('statement_of_funding_source_categories')) {
            Schema::create('statement_of_funding_source_categories', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('statement_of_funding_source_id');
                $table->string('category_number', 50);
                $table->string('category_name');
                $table->decimal('amount', 15, 2)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('statement_of_funding_source_id', 'sfs_categories_sfs_id_fk')
                    ->references('id')
                    ->on('statement_of_funding_sources')
                    ->cascadeOnDelete();
            });
        }

        if (!Schema::hasTable('statement_of_funding_source_items')) {
            Schema::create('statement_of_funding_source_items', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('statement_of_funding_source_category_id');
                $table->text('particulars');
                $table->string('account_classification')->nullable();
                $table->decimal('amount', 15, 2)->nullable();
                $table->unsignedInteger('sort_order')->default(0);
                $table->timestamps();

                $table->foreign('statement_of_funding_source_category_id', 'sfs_items_category_id_fk')
                    ->references('id')
                    ->on('statement_of_funding_source_categories')
                    ->cascadeOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('statement_of_funding_source_items');
        Schema::dropIfExists('statement_of_funding_source_categories');
        Schema::dropIfExists('statement_of_funding_sources');
    }
};
