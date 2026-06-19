<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('legal_nodes', function (Blueprint $table): void {
            $table->bigIncrements('node_id');
            $table->unsignedBigInteger('act_id');
            $table->unsignedBigInteger('parent_node_id')->nullable();
            $table->enum('node_type', [
                'PART',
                'CHAPTER',
                'SECTION',
                'SUBSECTION',
                'PARAGRAPH',
                'SUBPARAGRAPH',
                'ITEM',
                'SCHEDULE',
                'PREAMBLE',
                'DEFINITIONS',
            ]);
            $table->string('node_no', 20)->nullable();
            $table->string('heading', 500)->nullable();
            $table->string('marginal_note', 1500)->nullable();
            $table->longText('text_content')->nullable();
            $table->unsignedInteger('sort_order')->default(0);
            $table->unsignedTinyInteger('level')->nullable();
            $table->boolean('has_children')->default(false);
            $table->enum('status', ['active', 'repealed', 'amended'])->default('active');
            $table->date('effective_date')->nullable();
            $table->date('repealed_date')->nullable();
            $table->unsignedBigInteger('replaced_by_node_id')->nullable();
            $table->unsignedBigInteger('replaces_node_id')->nullable();
            $table->unsignedTinyInteger('version_no')->default(1);
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('act_id')->references('act_id')->on('acts')->cascadeOnDelete();
            $table->foreign('parent_node_id')->references('node_id')->on('legal_nodes')->cascadeOnDelete();
            $table->foreign('replaced_by_node_id')->references('node_id')->on('legal_nodes')->nullOnDelete();
            $table->foreign('replaces_node_id')->references('node_id')->on('legal_nodes')->nullOnDelete();

            $table->index(['act_id', 'parent_node_id', 'sort_order']);
            $table->index(['act_id', 'node_type']);
            $table->fullText(['text_content', 'heading']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('legal_nodes');
    }
};
