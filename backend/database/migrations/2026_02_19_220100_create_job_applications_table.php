<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_id')->constrained('jobs_listing')->cascadeOnDelete();

            // Personal Info
            $table->string('full_name');
            $table->string('email');
            $table->string('phone');
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();

            // Education & Training
            $table->string('education_level')->nullable();
            $table->string('university')->nullable();
            $table->string('graduation_year')->nullable();
            $table->string('major')->nullable();
            $table->text('certifications')->nullable();

            // Experience
            $table->integer('years_of_experience')->nullable();
            $table->string('current_job')->nullable();
            $table->text('previous_experience')->nullable();
            $table->text('skills')->nullable();

            // Extra
            $table->text('cover_letter')->nullable();
            $table->string('cv_file')->nullable();
            $table->enum('status', ['pending', 'reviewed', 'shortlisted', 'rejected', 'accepted'])->default('pending');
            $table->text('admin_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
