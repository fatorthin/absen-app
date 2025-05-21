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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->string('name');
            $table->date('birthdate');
            $table->enum('gender', ['L', 'P']);
            $table->string('no_wa');
            // $table->enum('group', ['Petoran', 'Pucangsawit 1', 'Sekarpace']);
            // $table->string('group_id');
            $table->foreignId('group_id')->constrained(
                table: 'groups',
                indexName: 'students_group_id'
            );
            $table->enum('class', ['1 SMA', '2 SMA', '3 SMA', '1 SMP', '2 SMP', '3 SMP', 'GP MT']);
            $table->string('avatar')->nullable();
            $table->enum('status', ['aktif', 'nonaktif']);
            $table->string('parent_name');
            $table->string('parent_phone');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
