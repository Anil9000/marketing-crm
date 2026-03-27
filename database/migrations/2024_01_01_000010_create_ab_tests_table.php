<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ab_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->cascadeOnDelete();
            $table->string('variant_a_subject');
            $table->longText('variant_a_content');
            $table->string('variant_b_subject');
            $table->longText('variant_b_content');
            $table->enum('winner', ['a', 'b'])->nullable();
            $table->unsignedInteger('variant_a_opens')->default(0);
            $table->unsignedInteger('variant_b_opens')->default(0);
            $table->unsignedInteger('variant_a_clicks')->default(0);
            $table->unsignedInteger('variant_b_clicks')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ab_tests');
    }
};
