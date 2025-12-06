<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Drop foreign key constraint first
            $table->dropForeign(['account_id']);
            // Drop the account_id column
            $table->dropColumn('account_id');
        });
    }

    public function down(): void
    {
        Schema::table('accounts', function (Blueprint $table) {
            // Add back the account_id column
            $table->unsignedBigInteger('account_id')->nullable();
            // Add back the foreign key constraint
            $table->foreign('account_id')->references('id')->on('accounts')->onDelete('cascade');
        });
    }
};