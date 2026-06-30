<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('act_statistics', function (Blueprint $table): void {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('act_id')->unique();
            $table->unsignedInteger('sections_count')->default(0);
            $table->unsignedInteger('subsections_count')->default(0);
            $table->unsignedInteger('parts_count')->default(0);
            $table->unsignedInteger('chapters_count')->default(0);
            $table->unsignedInteger('paragraphs_count')->default(0);
            $table->unsignedInteger('items_count')->default(0);
            $table->unsignedInteger('total_nodes_count')->default(0);
            $table->timestamp('computed_at')->nullable();

            $table->foreign('act_id')->references('act_id')->on('acts')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('act_statistics');
    }
};
