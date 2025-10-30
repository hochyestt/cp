<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStatisticsTable extends Migration
{
    public function up()
    {
        Schema::create('statistics', function (Blueprint $table) {
            $table->id('stat_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('complet_tasks')->default(0);
            $table->integer('complet_habit')->default(0);
            $table->date('report');
            $table->timestamps();
            
            $table->index(['user_id', 'report']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('statistics');
    }
}