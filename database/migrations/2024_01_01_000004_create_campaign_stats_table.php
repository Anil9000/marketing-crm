<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('campaign_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('opens')->default(0);
            $table->unsignedInteger('clicks')->default(0);
            $table->unsignedInteger('conversions')->default(0);
            $table->unsignedInteger('bounces')->default(0);
            $table->unsignedInteger('unsubscribes')->default(0);
            $table->unsignedInteger('sent_count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('campaign_stats');
    }
};
