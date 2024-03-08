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
        Schema::create('endpoint_scope', function (Blueprint $table) {
            $table->foreignId('endpoint_id')->constrained('endpoints')->onDelete('cascade');
            $table->foreignId('scope_id')->constrained('scopes')->onDelete('cascade');
            $table->primary(['endpoint_id', 'scope_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('endpoint_scope');
    }
};
