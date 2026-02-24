<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('return_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->constrained()->onDelete('cascade');
            $table->string('return_number')->unique();
            $table->enum('status', ['requested', 'approved', 'rejected', 'completed'])->default('requested');
            $table->string('reason');
            $table->text('details')->nullable();
            $table->json('items')->nullable(); // Items being returned [{order_item_id, quantity, reason}]
            $table->decimal('refund_amount', 10, 2)->nullable();
            $table->string('refund_method')->nullable(); // original, store_credit, manual
            $table->text('admin_notes')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->foreignId('processed_by')->nullable()->constrained('customers')->nullOnDelete();
            $table->timestamps();

            $table->index(['customer_id', 'status']);
            $table->index(['order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('return_requests');
    }
};
