<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('case_laws', function (Blueprint $table) {
            $table->id();
            $table->string('case_name', 500);
            $table->longText('content');
            $table->enum('status', ['active', 'overruled'])->default('active');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('case_laws');
    }
};
