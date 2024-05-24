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
        Schema::create('sys_file', function (Blueprint $table) {
            $table->uuid('id')->primary();

			$table->uuidMorphs('model');
            $table->string('slug')->unique();
            $table->string('folder');
            $table->string('original_name');
            $table->string('file_name');
            $table->string('mime_type')->nullable();
            $table->string('disk');
            $table->string('file_path');
            $table->json('properties');
            $table->unsignedBigInteger('size');
            $table->unsignedInteger('version')->nullable()->index();

            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Schema::dropIfExists('sys_file');
    }
};
