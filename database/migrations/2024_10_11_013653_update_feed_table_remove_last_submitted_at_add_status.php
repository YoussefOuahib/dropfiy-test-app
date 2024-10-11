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
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('last_submitted_at');
            $table->enum('status', [
                'pending',
                'synced',
                'failed'
            ])->after('id')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('feeds', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->timestamp('last_submitted_at')->nullable();
        });
    }
};
