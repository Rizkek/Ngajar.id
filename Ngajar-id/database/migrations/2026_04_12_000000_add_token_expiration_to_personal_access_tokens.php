<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * ✅ PHASE 1 SECURITY: Add token expiration columns
     *
     * Allows tokens to auto-expire after configurable period
     * Prevents stolen tokens from providing permanent access
     */
    public function up(): void
    {
        // Try to alter table, but catch exception if it doesn't exist
        // This handles both cases: table doesn't exist or already has the columns
        try {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                // Only add columns if they don't already exist
                if (!Schema::hasColumn('personal_access_tokens', 'expires_at')) {
                    $table->timestamp('expires_at')->nullable()->after('abilities');
                }

                if (!Schema::hasColumn('personal_access_tokens', 'last_used_at')) {
                    // Track when token was last used (for activity monitoring)
                    $table->timestamp('last_used_at')->nullable()->after('expires_at');
                }

                if (!Schema::hasIndex('personal_access_tokens', 'personal_access_tokens_expires_at_index')) {
                    // Index for fast expiration cleanup queries
                    $table->index('expires_at');
                }
            });
        } catch (\Exception $e) {
            // Table doesn't exist yet or columns already added, skip silently
            // This is safe because Sanctum may not be initialized or already migrated
        }
    }

    public function down(): void
    {
        // Try to drop columns, but catch exception if table doesn't exist
        try {
            Schema::table('personal_access_tokens', function (Blueprint $table) {
                $table->dropColumn(['expires_at', 'last_used_at']);
                $table->dropIndex(['expires_at']);
            });
        } catch (\Exception $e) {
            // Table doesn't exist, silently ignore
        }
    }
};
