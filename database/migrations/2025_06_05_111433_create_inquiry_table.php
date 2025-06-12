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
        Schema::create('inquiry', function (Blueprint $table) {
            $table->id();
            //Foreign keys
            $table->unsignedBigInteger('PublicUser_id');
            $table->unsignedBigInteger('MCMC_id')->nullable();
            $table->unsignedBigInteger('Agency_id')->nullable();
            
            $table->String('NewsTitle');
            $table->String('NewsContent');
            $table->String('NewsSource');
            $table->String('InquiryDate');
            $table->String('InquiryStatus')->default('Pending'); // Default status is 'Pending'
            $table->string('attachment')->nullable(); // <-- Attachment column
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inquiry');
    }
};
