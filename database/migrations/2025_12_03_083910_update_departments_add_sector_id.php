<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            // Add sector_id
            $table->unsignedBigInteger('sector_id')->nullable()->after('name');

            // Add foreign key
            $table->foreign('sector_id')->references('id')->on('sectors')->onDelete('set null');

            // Drop old sector_name column
            $table->dropColumn('sector_name');
        });
    }

    public function down(): void
    {
        Schema::table('departments', function (Blueprint $table) {
            $table->string('sector_name')->after('name');
            $table->dropForeign(['sector_id']);
            $table->dropColumn('sector_id');
        });
    }
};

