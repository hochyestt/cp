<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_telegram_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTelegramUsersTable extends Migration
{
    public function up()
    {
        Schema::create('telegram_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->bigInteger('telegram_id');
            $table->timestamps();
            
            $table->unique(['user_id', 'telegram_id']);
            $table->index('telegram_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('telegram_users');
    }
}