<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('insurance_schemes', function (Blueprint $table) {
            $table->boolean('is_active')->default(true)->after('copayment_percentage');
        });
    }
    public function down(): void {
        Schema::table('insurance_schemes', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });
    }
};