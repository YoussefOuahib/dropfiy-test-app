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
        Schema::table('user_settings', function (Blueprint $table) {
            $table->integer('sync_time')->default(120);
            $table->string('currency', 3)->nullable();
            $table->timestamp('last_auto_synced_at')->nullable();
            $table->dropColumn(['key', 'value']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn(['sync_time', 'currency', 'last_auto_synced_at']);
            $table->string('key');
            $table->text('value')->nullable();
        });
    }
};