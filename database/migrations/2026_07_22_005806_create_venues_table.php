<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city');
            $table->string('address');
            $table->string('state', 2);
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->boolean('verified')->default(false);
            $table->boolean('featured')->default(false);
            $table->string('neighborhood')->nullable()->index();
            $table->timestamps();

            $table->index(['city', 'state']);
            $table->index(['featured', 'verified']);
            $table->index(['latitude', 'longitude']);
            $table->index(['city', 'state', 'featured', 'verified']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
