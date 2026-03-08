<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('assignee_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('description', 255)->nullable();
            $table->text('content')->nullable();
            $table->string('status')->default('pending')->index();
            $table->dateTime('due_at')->nullable();
            // $table->dateTime('created_at')->nullable();
            // $table->dateTime('updated_at')->nullable();
            // $table->dateTime('deleted_at')->nullable();
            $table->datetimes();
            $table->softDeletesDatetime();

            $table->index(['user_id', 'status']);
            $table->index(['assignee_id', 'status']);
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
