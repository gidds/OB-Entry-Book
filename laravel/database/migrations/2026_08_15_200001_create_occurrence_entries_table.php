<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('occurrence_entries', function (Blueprint $table): void {
            $table->id();
            $table->string('ob_number')->unique();
            $table->date('occurred_on')->index();
            $table->string('customer')->nullable()->index();
            $table->text('entry_text');
            $table->string('legacy_id')->nullable()->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('occurrence_entries');
    }
};
