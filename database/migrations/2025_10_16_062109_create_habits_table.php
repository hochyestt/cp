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
        Schema::create('habits', function (Blueprint $table) {
            $table->id();
            $table->string('name', 45);
            $table->string('progress', 45)->default('0%');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('frequency_type', ['day', 'week'])->default('day'); 
            $table->unsignedSmallInteger('frequency_value')->default(1); 
            $table->unsignedSmallInteger('times_done_since_reset')->default(0); 
            $table->timestamp('counter_reset_at')->nullable(); 
            $table->timestamp('next_notification')->nullable();
            $table->timestamp('last_done_at')->nullable();

            $table->timestamps();
            
            $table->index(['user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('habits');
    }
};
// После этого не забудьте выполнить: php artisan migrate