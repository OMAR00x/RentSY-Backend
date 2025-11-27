<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartment_amenity', function (Blueprint $table) {
            $table->foreignId('apartment_id')->constrained()->onDelete('cascade');
            $table->foreignId('amenity_id')->constrained()->onDelete('cascade');
            $table->primary(['apartment_id', 'amenity_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartment_amenity');
    }
};
