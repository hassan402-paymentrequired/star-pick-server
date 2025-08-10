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
        Schema::create('virtual_accounts', function (Blueprint $table) {
            $table->id();
            $table->ulid('user_id');
            $table->foreign('user_id', 'va_user_id_fk')->references('id')->on('users')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('account_number', 10)->unique();
            $table->string('account_name');
            $table->string('bank_name');
            $table->string('bank_code');
            $table->string('paystack_customer_code')->nullable()->default(null)->index();
            $table->string('paystack_account_id')->nullable()->default(null)->index();
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->index();
            $table->json('meta_data')->nullable()->default(null);
            $table->timestamp('last_activity_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index('user_id', 'va_user_id_idx');
            $table->index('account_number', 'va_account_number_idx');
            $table->index('bank_code', 'va_bank_code_idx');
            $table->index('status', 'va_status_idx');
            $table->index('created_at', 'va_created_at_idx');
            $table->index('last_activity_at', 'va_last_activity_idx');

            // Ensure only one virtual account per user
            $table->unique('user_id', 'va_user_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('virtual_accounts');
    }
};
