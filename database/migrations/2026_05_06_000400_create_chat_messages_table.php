<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_messages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('chat_session_id')
                ->constrained('chat_sessions')
                ->cascadeOnDelete();
            $table->enum('role', ['user', 'assistant', 'system'])->index();
            $table->longText('content');
            $table->json('cited_section_ids')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_messages');
    }
};
