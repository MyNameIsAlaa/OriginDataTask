<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CeateProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('projects', function (Blueprint $table) {
        $table->bigIncrements('id');
        $table->unsignedBigInteger('company_id');
        $table->foreign('company_id')
        ->references('id')
        ->on('companies')
        ->onDelete('cascade');
        $table->unsignedBigInteger('employee_id');
        $table->foreign('employee_id')
        ->references('id')
        ->on('employees')
        ->onDelete('cascade');
        $table->string('title');
        $table->string('description');
        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::dropIfExists('projects');
    }
}
