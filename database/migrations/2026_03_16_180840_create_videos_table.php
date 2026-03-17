<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('videos', function (Blueprint $table) {
            $table->uuid('uuid')->primary();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('filename')->unique();
            $table->string('original_filename');
            $table->string('path');
            $table->string('thumbnail_path');
            $table->integer('size');
            $table->string('mime_type');
            $table->date('upload_date');
            $table->string('upload_month')->index();
            $table->text('caption')->nullable();
            $table->timestamps();
            
            // Indexes untuk optimasi query
            $table->index(['user_id', 'upload_month']);
            $table->index('upload_date');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('videos');
    }
};
