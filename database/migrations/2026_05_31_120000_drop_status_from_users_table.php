<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'status')) {
            return;
        }

        DB::table('users')
            ->whereIn('status', ['inactive', 'banned'])
            ->update(['is_active' => false]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'status')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active')->after('password');
        });

        DB::table('users')
            ->where('is_active', false)
            ->update(['status' => 'inactive']);

        DB::table('users')
            ->where('is_active', true)
            ->update(['status' => 'active']);
    }
};
