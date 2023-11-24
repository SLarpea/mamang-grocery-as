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
        Schema::table('carousels', function (Blueprint $table) {
            $table->string('dots')->default('false')->after('type');
            $table->string('arrows')->default('false')->after('dots');
            $table->string('accessibility')->default('true')->after('arrows');
            $table->integer('rows')->default(1)->after('accessibility');
            $table->integer('slides_to_show')->default(1)->after('rows');
            $table->integer('slides_per_row')->default(1)->after('slides_to_show');
            $table->integer('autoplay_speed')->default(1000)->after('slides_per_row');
            $table->string('center_padding')->default('0')->after('autoplay_speed');
            $table->string('pause_on_hover')->default('true')->after('center_padding');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carousels', function (Blueprint $table) {
            //
        });
    }
};
