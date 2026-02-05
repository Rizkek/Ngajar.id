<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $functions = ['create_user_token', 'update_token_on_topup'];

        foreach ($functions as $function) {
            $rows = DB::select("
                SELECT oid, pg_get_function_identity_arguments(oid) as args
                FROM pg_proc
                WHERE proname = ? AND pronamespace = 'public'::regnamespace
            ", [$function]);

            foreach ($rows as $row) {
                DB::statement("ALTER FUNCTION public.{$function}({$row->args}) SET search_path = public, pg_temp");
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $functions = ['create_user_token', 'update_token_on_topup'];

        foreach ($functions as $function) {
            $rows = DB::select("
                SELECT oid, pg_get_function_identity_arguments(oid) as args
                FROM pg_proc
                WHERE proname = ? AND pronamespace = 'public'::regnamespace
            ", [$function]);

            foreach ($rows as $row) {
                DB::statement("ALTER FUNCTION public.{$function}({$row->args}) RESET search_path");
            }
        }
    }
};
