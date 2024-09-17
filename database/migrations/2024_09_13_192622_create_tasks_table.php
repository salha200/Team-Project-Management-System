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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');

            // Define the column first
            $table->unsignedBigInteger('assigned_user_id')->nullable();
            // Then define the foreign key constraint
            $table->foreign('assigned_user_id')->references('id')->on('users')->onDelete('set null');

            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['new', 'in_progress', 'completed'])->default('new');
            $table->enum('priority', ['low', 'medium', 'high'])->default('medium');
            $table->date('due_date')->nullable();
            $table->text('note')->nullable()->after('due_date');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
