<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('form_signatories', function (Blueprint $table) {
            // Add form_id column
            $table->foreignId('form_id')->nullable()->after('id')->constrained('forms')->onDelete('cascade');
            
            // Keep form_name for now to migrate data, but make it nullable
            $table->string('form_name')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('form_signatories', function (Blueprint $table) {
            $table->dropForeign(['form_id']);
            $table->dropColumn('form_id');
            $table->string('form_name')->nullable(false)->change();
        });
    }
};