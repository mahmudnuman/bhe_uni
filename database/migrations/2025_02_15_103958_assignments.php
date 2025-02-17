<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads')->onDelete('cascade');
            $table->foreignId('counselor_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['In Progress', 'Bad Timing', 'Not Interested', 'Not Qualified'])
            ->default('In Progress');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('assignments');
    }
};
