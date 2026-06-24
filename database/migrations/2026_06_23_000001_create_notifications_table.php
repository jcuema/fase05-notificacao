<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('video_id')->unique();    // unique: garante idempotência
            $table->unsignedBigInteger('user_id')->index();
            $table->string('email_address');
            $table->string('channel')->default('email');
            $table->string('video_status');          // completed | failed
            $table->string('notification_status')->default('pending'); // pending | sent | delivered | failed
            $table->string('result_url')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
