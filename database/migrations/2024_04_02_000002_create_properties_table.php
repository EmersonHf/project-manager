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
            $table->string('name');
            $table->string('title');
            $table->text('description');
            $table->text('address');
            $table->string('city', 100);
            $table->string('state', 100);
            $table->string('zip_code', 20);
            $table->string('country', 100);
            $table->decimal('price', 12, 2);
            $table->enum('type', ['house', 'apartment', 'condo', 'townhouse', 'land']);
            $table->enum('status', ['available', 'rented', 'maintenance', 'sold']);
            $table->enum('listing_type', ['sale', 'rent']);
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->integer('square_feet')->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->decimal('monthly_rent', 12, 2)->nullable();
            $table->decimal('deposit_amount', 12, 2)->nullable();
            $table->date('available_date');
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('manager_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
}; 