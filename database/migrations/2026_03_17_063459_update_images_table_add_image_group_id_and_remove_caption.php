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
        Schema::table('images', function (Blueprint $table) {
            // Add image_group_id column
            $table->uuid('image_group_id')->nullable()->after('user_id');
            $table->foreign('image_group_id')->references('uuid')->on('image_groups')->onDelete('cascade');
            $table->index('image_group_id');

            // Remove caption column (now stored in image_groups table)
            $table->dropColumn('caption');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('images', function (Blueprint $table) {
            // Drop image_group_id column
            $table->dropForeign(['image_group_id']);
            $table->dropIndex(['image_group_id']);
            $table->dropColumn('image_group_id');

            // Add back caption column
            $table->text('caption')->nullable()->after('mime_type');
        });
    }
};
