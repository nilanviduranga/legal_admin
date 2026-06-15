<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('acts', function (Blueprint $table): void {
            $table->bigIncrements('act_id');
            $table->unsignedBigInteger('gazette_id');
            $table->unsignedSmallInteger('act_no');
            $table->unsignedSmallInteger('act_year');
            $table->string('title', 500);
            $table->string('short_title', 200)->nullable();
            $table->date('commencement_date')->nullable();
            $table->enum('status', ['active', 'repealed', 'amended'])->default('active');
            $table->unsignedBigInteger('replaced_by_act_id')->nullable();
            $table->unsignedBigInteger('replaces_act_id')->nullable();
            $table->boolean('current_version')->default(true);
            $table->timestamps();

            $table->foreign('gazette_id')->references('gazette_id')->on('gazettes')->cascadeOnDelete();
            $table->foreign('replaced_by_act_id')->references('act_id')->on('acts')->nullOnDelete();
            $table->foreign('replaces_act_id')->references('act_id')->on('acts')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('acts');
    }
};
