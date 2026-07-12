<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
        Schema::create('risk_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('country_id')->constrained()->onDelete('cascade');
            $table->double('weather_risk');
            $table->double('inflation_risk');
            $table->double('currency_risk');
            $table->double('news_risk');
            $table->double('total_risk_score');
            $table->string('risk_status');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('risk_scores');
    }
};
