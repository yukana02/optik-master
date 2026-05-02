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
        Schema::create('refractions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medical_record_id')->constrained()->cascadeOnDelete();

            $table->string('doctor_name')->nullable();
            $table->string('diagnosis')->nullable();
            $table->date('exam_date')->nullable();
            $table->text('notes')->nullable();

            // OD
            $table->string('od_sc')->nullable();
            $table->decimal('od_sph', 5, 2)->nullable();
            $table->decimal('od_cyl', 5, 2)->nullable();
            $table->decimal('od_axis', 5, 2)->nullable();
            $table->decimal('od_add', 5, 2)->nullable();
            $table->decimal('od_pd', 5, 2)->nullable();
            $table->string('od_prism')->nullable();
            $table->string('od_cc')->nullable();

            // OS
            $table->string('os_sc')->nullable();
            $table->decimal('os_sph', 5, 2)->nullable();
            $table->decimal('os_cyl', 5, 2)->nullable();
            $table->decimal('os_axis', 5, 2)->nullable();
            $table->decimal('os_add', 5, 2)->nullable();
            $table->decimal('os_pd', 5, 2)->nullable();
            $table->string('os_prism')->nullable();
            $table->string('os_cc')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('refractions');
    }
};
