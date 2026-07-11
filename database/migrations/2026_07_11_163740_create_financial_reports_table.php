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
        Schema::create('financial_reports', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['manual', 'otomatis']);
            $table->enum('category', ['pemasukan', 'pengeluaran']);
            $table->decimal('amount', 10, 2);
            $table->string('description')->nullable();
            $table->date('report_date');
            $table->foreignId('order_id')->nullable()->constrained('orders');
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_reports');
    }
};
