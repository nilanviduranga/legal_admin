<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('act_ai_metadata', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('act_id')->unique();
            $table->text('summary')->nullable();
            $table->json('keywords')->nullable();
            $table->json('legal_domains')->nullable();
            $table->json('regulated_activities')->nullable();
            $table->json('subjects')->nullable();
            $table->timestamp('generated_at')->nullable();
            $table->timestamps();

            $table->foreign('act_id')->references('act_id')->on('acts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('act_ai_metadata');
    }
};
