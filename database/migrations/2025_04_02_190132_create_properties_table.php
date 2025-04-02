<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title');
            $table->text('description');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->string('zip_code');
            $table->string('country');
            $table->decimal('price', 12, 2);
            $table->enum('type', ['house', 'apartment', 'condo', 'land', 'commercial']);
            $table->enum('status', ['available', 'rented', 'sold', 'maintenance']);
            $table->enum('listing_type', ['sale', 'rent']);
            $table->integer('bedrooms')->nullable();
            $table->integer('bathrooms')->nullable();
            $table->decimal('square_feet', 10, 2)->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            $table->decimal('monthly_rent', 10, 2)->nullable();
            $table->decimal('deposit_amount', 10, 2)->nullable();
            $table->date('available_date')->nullable();
            $table->foreignId('owner_id')->constrained('users');
            $table->foreignId('manager_id')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
