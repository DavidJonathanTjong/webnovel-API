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
        Schema::create('detailnovel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_novel');
            $table->integer('chapter_novel');
            $table->string('text_novel');
            $table->timestamps();
            //foregin key ke tabel novel
            $table->foreign('id_novel')->references('id')->on('novels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailnovel');
    }
};