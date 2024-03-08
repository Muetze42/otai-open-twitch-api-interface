<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('endpoints', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Resource::class)
                ->constrained()
                ->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description');
            $table->text('instruction');
            $table->text('authorization');
            $table->text('route');
            $table->enum('method', ['GET', 'POST', 'PUT', 'PATCH', 'DELETE']);

            $table->json('request_body')->nullable();
            $table->json('request_query_parameters')->nullable();
            $table->json('response_body')->nullable();
            $table->json('response_codes')->nullable();

            $table->boolean('user_access_tokens_auth');
            $table->boolean('app_access_token_auth');

            $table->boolean('active')->default(true);
            $table->unsignedBigInteger('batch')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('endpoints');
    }
};
