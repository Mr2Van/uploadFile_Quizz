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
        Schema::create('participations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
            ->constrained('User')
            ->onDelete('cascade');
            $table->foreignId('qcms_id')
            ->constrained('qcms')
            ->onDelete('cascade');
            $table->integer('total_points');
            $table->integer('score');
            $table->date('date_participation');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participations');
    }
};
