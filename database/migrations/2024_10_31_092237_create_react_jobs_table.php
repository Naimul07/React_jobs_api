<?php

use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('react_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->foreignIdFor(User::class)->constrained()->onDelete('cascade');
            $table->string('type');
            $table->text('description');
            $table->string('salary');
            $table->string('location');
            $table->string('company_name');
            $table->text('company_description');
            $table->string('company_email');
            $table->string('company_phone');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('react_jobs');
    }
};
