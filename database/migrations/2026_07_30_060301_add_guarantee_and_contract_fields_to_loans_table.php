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
        Schema::table('loans', function (Blueprint $table) {
            $table->string('guarantee_type')->nullable()->after('status');
            $table->text('guarantee_details')->nullable()->after('guarantee_type');
            $table->string('contract_type')->nullable()->after('guarantee_details');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['guarantee_type', 'guarantee_details', 'contract_type']);
        });
    }
};
