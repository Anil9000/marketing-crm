<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->foreignId('contact_id')->nullable()->constrained()->nullOnDelete();
            $table->enum('event_type', ['sent', 'open', 'click', 'bounce', 'unsubscribe']);
            $table->json('metadata')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->string('tracking_token')->nullable()->index();
            $table->timestamps();

            $table->index(['campaign_id', 'event_type']);
            $table->index(['contact_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_events');
    }
};
