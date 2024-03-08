<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('event_sub_scope', function (Blueprint $table) {
            $table->foreignId('event_sub_id')->constrained('event_subs')->onDelete('cascade');
            $table->foreignId('scope_id')->constrained('scopes')->onDelete('cascade');
            $table->primary(['event_sub_id', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('event_sub_scope');
    }
};
