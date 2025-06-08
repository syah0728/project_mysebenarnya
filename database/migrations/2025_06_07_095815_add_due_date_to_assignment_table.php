<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('assignment')) {
            Schema::table('assignment', function (Blueprint $table) {
                if (!Schema::hasColumn('assignment', 'due_date')) {
                    $table->date('due_date')->nullable()->after('AssignmentDate');
                }
                if (!Schema::hasColumn('assignment', 'comments')) {
                    $table->text('comments')->nullable()->after('due_date');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::table('assignment', function (Blueprint $table) {
            $table->dropColumn(['due_date', 'comments']);
        });
    }
};