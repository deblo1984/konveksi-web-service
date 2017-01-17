<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 30)->unique();
            $table->string('name', 70);
            $table->integer('user_id');
            $table->text('description')->nullable();
            $table->string('image_path')->nullable();
            $table->double('qty')->nullable();
            $table->double('cost')->nullable();
            $table->double('total')->nullable();
            $table->dateTime('order_date')->nullable();
            $table->dateTime('due_date')->nullable();
            $table->string('status')->nullable()->default('pending');
            $table->dateTime('finish_date')->nullable();
            $table->string('is_finish')->nullable()->default('F');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
}
