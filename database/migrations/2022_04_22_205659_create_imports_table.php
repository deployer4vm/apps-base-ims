<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imports', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('tenant_id')->default(0);
            $table->bigInteger('user_id')->default(0);
            $table->string('cache_key')->default('');
            $table->bigInteger('jobs_id')->default(0);
            $table->string('process_id')->default('');
            $table->string('queue')->default('');
            $table->dateTime('job_dispatch_time')->nullable();
            $table->dateTime('job_start_time')->nullable();
            $table->longText('log')->nullable();
            $table->longText('data')->nullable();
            $table->unsignedTinyInteger('status')->default(0);

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
        Schema::dropIfExists('imports');
    }
}
