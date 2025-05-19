<?php

use App\Models\Group;
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
        Schema::create('groups', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->enum('main_group_name', ['Desa Timur 1', 'Desa Timur 2']);
            $table->timestamps();
            $table->softDeletes();
        });

        Group::insert([
            [
                'name' => 'Petoran',
                'main_group_name' => 'Desa Timur 2',
            ],
            [
                'name' => 'Pucangsawit 1',
                'main_group_name' => 'Desa Timur 2',
            ],
            [
                'name' => 'Sekarpace',
                'main_group_name' => 'Desa Timur 2',
            ],
            [
                'name' => 'Pucangsawit 2',
                'main_group_name' => 'Desa Timur 2',
            ],
            [
                'name' => 'Pucangsawit Indah',
                'main_group_name' => 'Desa Timur 2',
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('groups');
    }
};
