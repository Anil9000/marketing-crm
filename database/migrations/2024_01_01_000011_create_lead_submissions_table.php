<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * NOTE: The `lead_submissions` table is already created in migration 000009.
 * This migration is a no-op placeholder kept for reference.
 * If you need to extend lead_submissions in a fresh project, delete migration 000009
 * and use this one instead (or add an alter table migration with a new timestamp).
 */
return new class extends Migration
{
    public function up(): void
    {
        // lead_submissions was created in 2024_01_01_000009.
        // This migration intentionally does nothing to avoid a duplicate table error.
    }

    public function down(): void
    {
        // Nothing to roll back.
    }
};
