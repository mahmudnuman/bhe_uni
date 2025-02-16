<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->enum('status', ['In Progress', 'Approved', 'Rejected'])->default('In Progress');
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('applications');
    }
};
