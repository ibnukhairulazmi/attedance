<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kelas_id')
                ->constrained('kelas')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            $table->string('nim')->unique();
            $table->string('nama');
            $table->string('email')->unique();

            $table->string('password');

            $table->string('device_id')->nullable();
            $table->string('device_token')->nullable();

            $table->text('api_token')->nullable();

            $table->boolean('is_locked')
                ->default(false);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mahasiswa');
    }
};
