<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campus_applications', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email');
            $table->string('slug')->unique();
            $table->string('status')->default('pending')->index();
            $table->foreignId('campus_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->string('invitation_token_hash')->nullable();
            $table->timestamp('invitation_expires_at')->nullable();
            $table->timestamp('admin_created_at')->nullable();
            $table->timestamps();
            $table->index(['email', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campus_applications');
    }
};
