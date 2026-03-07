<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('department_stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('department_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('quantity_remaining')->default(0);
            $table->timestamps();
            $table->unique(['batch_id', 'department_id']);
        });
    }
    public function down(): void { Schema::dropIfExists('department_stock'); }
};
