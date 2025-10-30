<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
         Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title', 45);
            $table->string('description', 45)->nullable();
            $table->string('status', 45)->default('pending');
            $table->string('priority', 45)->default('medium');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
        
            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'priority']);
        });
    }

    
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
