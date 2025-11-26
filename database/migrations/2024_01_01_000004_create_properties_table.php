<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->enum('price_type', ['daily', 'weekly', 'monthly'])->default('daily');
            $table->integer('rooms');
            $table->integer('baths')->default(1);
            $table->decimal('area', 10, 2)->nullable();
            $table->integer('floor')->nullable();
            $table->boolean('furnished')->default(false);
            $table->boolean('has_internet')->default(false);
            $table->enum('property_type', ['apartment', 'studio', 'office', 'other'])->default('apartment');
            $table->enum('status', ['active', 'inactive', 'rented', 'pending'])->default('pending');
            $table->string('address')->nullable();
            $table->timestamps();
            
            $table->index(['city_id', 'price', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
