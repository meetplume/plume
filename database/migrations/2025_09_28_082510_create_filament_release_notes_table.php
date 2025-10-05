<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('release_notes', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('version');
            $table->text('notes');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('release_notes');
    }
};
