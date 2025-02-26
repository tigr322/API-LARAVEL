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
        Schema::create('pc_config', function (Blueprint $table) {
            $table->id(); // Добавляем первичный ключ
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('motherboard');
            $table->string('graphic_card')->nullable();
            $table->string('ram');
            $table->string('processor');
            $table->timestamps(); // Добавляем created_at и updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pc_config');
    }
};
