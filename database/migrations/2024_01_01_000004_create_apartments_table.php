<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->enum('price_type', ['daily'])->default('daily');
            $table->integer('rooms');
            $table->enum('status', ['active', 'rented', 'pending'])->default('active');
            $table->string('address')->nullable();
            $table->timestamps();

            $table->index(['city_id', 'price', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
