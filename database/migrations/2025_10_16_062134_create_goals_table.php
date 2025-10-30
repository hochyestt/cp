<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGoalsTable extends Migration // Название класса в PascalCase, множественное число
{
    public function up()
    {
        Schema::create('goals', function (Blueprint $table) { // Название таблицы во множественном числе
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Цель принадлежит пользователю
            $table->string('name', 155);
            $table->float('progress')->default(0); // Прогресс как число от 0 до 100
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('goals');
    }
}