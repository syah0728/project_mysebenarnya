<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('assignment', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('Inquiry_id');
            $table->unsignedBigInteger('Agency_id');
            $table->unsignedBigInteger('PublicUser_id');
            $table->date('AssignmentDate');
            $table->string('AssignmentStatus')->default('Assigned'); // Default status is 'Assigned'

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assignment');
    }
};
