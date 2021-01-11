<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWhisperMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('whisper_messages', function (Blueprint $table) {
            $table->id();
            $table->auth(); 
            $table->uuid('uuid')->index(); 
            $table->auth('recipient'); 
            $table->text('message')->nullable(); 
            $table->timestamp('read_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->foreignId('message_id')->nullable()->constrained('whisper_messages'); 
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
        Schema::dropIfExists('whisper_messages');
    }
}
