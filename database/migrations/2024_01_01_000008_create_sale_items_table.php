<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->constrained();
            $table->unsignedInteger('quantity');
            $table->decimal('selling_price', 12, 2);
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('profit', 12, 2);
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('sale_items'); }
};
