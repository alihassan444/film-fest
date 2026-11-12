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
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('email');
                $table->string('phone')->nullable();
                $table->string('student_id')->nullable();
                $table->string('department')->nullable();
                $table->string('semester')->nullable();
                $table->string('transaction_screenshot')->nullable();
                $table->string('ticket_id')->unique();
                $table->enum('status', ['pending', 'verified', 'checked_in', 'rejected'])->default('pending');
                $table->string('verification_token')->nullable()->unique();
                $table->timestamp('verified_at')->nullable();
                $table->timestamp('checked_in_at')->nullable();
                $table->string('qr_code_path')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();

                $table->index('ticket_id');
                $table->index('status');
                $table->index('verification_token');
                $table->index('email');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
