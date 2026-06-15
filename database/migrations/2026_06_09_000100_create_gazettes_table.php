<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('gazettes', function (Blueprint $table): void {
            $table->bigIncrements('gazette_id');
            $table->string('gazette_no', 50);
            $table->date('gazette_date');
            $table->string('title', 500);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gazettes');
    }
};
