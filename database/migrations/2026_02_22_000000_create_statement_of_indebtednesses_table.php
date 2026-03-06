<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('statement_of_indebtednesses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fiscal_year_id')->constrained()->cascadeOnDelete();
            $table->string('creditor');
            $table->date('date_contracted');
            $table->string('term_maturity');
            $table->decimal('principal_amount', 15, 2);
            $table->text('purpose');
            $table->decimal('prev_principal', 15, 2)->default(0);
            $table->decimal('prev_interest', 15, 2)->default(0);
            $table->decimal('prev_total', 15, 2)->default(0);
            $table->decimal('due_principal', 15, 2)->default(0);
            $table->decimal('due_interest', 15, 2)->default(0);
            $table->decimal('due_total', 15, 2)->default(0);
            $table->decimal('balance', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('statement_of_indebtednesses');
    }
};
