<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cross_references', function (Blueprint $table): void {
            $table->bigIncrements('reference_id');
            $table->unsignedBigInteger('source_node_id');
            $table->unsignedBigInteger('target_node_id');
            $table->string('reference_text', 500)->nullable();
            $table->enum('reference_type', [
                'refers_to', 'subject_to', 'as_amended_by',
                'repeals', 'saves', 'cross_applies',
            ])->default('refers_to');
            $table->timestamps();

            $table->foreign('source_node_id')->references('node_id')->on('legal_nodes')->cascadeOnDelete();
            $table->foreign('target_node_id')->references('node_id')->on('legal_nodes')->cascadeOnDelete();

            $table->index('source_node_id');
            $table->index('target_node_id');
            $table->unique(['source_node_id', 'target_node_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cross_references');
    }
};
