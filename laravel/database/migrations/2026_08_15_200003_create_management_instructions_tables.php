<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('management_instructions', function (Blueprint $table): void {
            $table->id();
            $table->date('instruction_date')->index();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('manager_name');
            $table->text('instruction_text');
            $table->string('legacy_id')->nullable()->unique();
            $table->timestamp('legacy_entry_time')->nullable();
            $table->timestamps();
        });

        Schema::create('instruction_acknowledgements', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('management_instruction_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('operator_name');
            $table->timestamp('acknowledged_at');
            $table->unique(['management_instruction_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instruction_acknowledgements');
        Schema::dropIfExists('management_instructions');
    }
};
