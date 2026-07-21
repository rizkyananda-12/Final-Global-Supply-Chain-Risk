<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void {
       Schema::create('ports', function (Blueprint $table) {
        $table->id();
        $table->string('port_name');             // Contoh: Port of Tanjung Priok
        $table->string('country_code');     // Contoh: ID, DE, SG
        $table->decimal('latitude', 10, 7); // Koordinat Lat
        $table->decimal('longitude', 10, 7);// Koordinat Long
        $table->enum('status', ['active', 'congested', 'closed'])->default('active');
        $table->integer('risk_level')->default(10); // Risk score (0-100)
        $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('ports');
    }
};
