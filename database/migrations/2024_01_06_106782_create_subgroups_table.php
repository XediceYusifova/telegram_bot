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
        Schema::create('subgroups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('group_id'); // groups yəni qruplar
            $table->foreign('group_id')->references('id')->on('groups')->onDelete('cascade');
            $table->string('title_az');
            $table->string('title_en');
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subgroups');
    }
};
