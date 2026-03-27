<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('segment_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->enum('type', ['email', 'sms', 'push_notification', 'social_media'])->default('email');
            $table->enum('status', ['draft', 'scheduled', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->string('subject')->nullable();
            $table->longText('content')->nullable();
            $table->decimal('budget', 10, 2)->default(0);
            $table->decimal('spent', 10, 2)->default(0);
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->boolean('ab_test_enabled')->default(false);
            $table->longText('variant_a')->nullable();
            $table->longText('variant_b')->nullable();
            $table->enum('frequency', ['one_time', 'daily', 'weekly', 'monthly'])->default('one_time');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
