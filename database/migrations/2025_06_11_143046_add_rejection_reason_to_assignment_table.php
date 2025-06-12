<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('assignment', function (Blueprint $table) {
            $table->text('rejection_reason')->nullable()->after('AssignmentStatus');
        });
    }

    public function down()
    {
        Schema::table('assignment', function (Blueprint $table) {
            $table->dropColumn('rejection_reason');
        });
    }

};
