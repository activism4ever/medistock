<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medicine_id')->constrained()->cascadeOnDelete();
            $table->string('batch_number')->unique();
            $table->date('expiry_date');
            $table->decimal('purchase_price', 12, 2);
            $table->decimal('margin_percentage', 5, 2)->default(0);
            $table->decimal('selling_price', 12, 2);
            $table->string('receipt_no')->nullable();
            $table->string('invoice_no')->nullable();
            $table->unsignedInteger('quantity_purchased');
            $table->unsignedInteger('quantity_remaining');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('batches'); }
};
