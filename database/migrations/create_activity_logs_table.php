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
        $tableName = config('activitylog.table', 'activity_logs');
        
        Schema::create($tableName, function (Blueprint $table) {
            $table->id();

            $table->string('event')->nullable();
            $table->text('description')->nullable();

            $table->nullableMorphs('subject');
            $table->nullableMorphs('causer');

            $table->json('properties')->nullable();

            // IPv6 addresses can be up to 45 characters
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 1024)->nullable();

            $table->timestamps();

            // Indexes for common queries
            $table->index('event');
            $table->index('created_at');
            
            // Composite indexes for polymorphic relationships
            $table->index(['subject_id', 'subject_type']);
            $table->index(['causer_id', 'causer_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        $tableName = config('activitylog.table', 'activity_logs');
        Schema::dropIfExists($tableName);
    }
};
