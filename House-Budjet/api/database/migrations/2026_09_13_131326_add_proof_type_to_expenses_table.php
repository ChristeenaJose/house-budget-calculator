<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('expenses', 'proof_type')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->string('proof_type')->default('none');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('expenses', 'proof_type')) {
            Schema::table('expenses', function (Blueprint $table) {
                $table->dropColumn('proof_type');
            });
        }
    }
};
