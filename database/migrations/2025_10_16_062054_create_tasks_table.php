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
            $table->string('yandex_event_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->dateTime('start_time')->nullable();
            $table->dateTime('end_time')->nullable(); // Сначала все основные поля
            $table->boolean('notified')->default(false); // Затем поля для бота
            $table->boolean('completed')->default(false); // И это тоже здесь
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'priority']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade'); // Хорошо бы добавить внешний ключ
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};