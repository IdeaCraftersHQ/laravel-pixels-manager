<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('pixels', function (Blueprint $table) {
            $table->id();
            $table->string('platform', 50)->index();
            $table->string('pixel_id', 255);
            $table->text('access_token')->nullable();
            $table->string('test_event_code', 100)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pixels');
    }
};
