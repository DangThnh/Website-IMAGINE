<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        

        // Schema::create('artworks', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->string('title');
        //     $table->text('description')->nullable();
        //     $table->string('image_url');
        //     $table->timestamps();
        // });

        // Schema::create('categories', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('name', 100)->unique();
        //     $table->timestamps();
        // });

        // Schema::create('artwork_categories', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('artwork_id')->constrained('images')->onDelete('cascade');
        //     $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        // });

        // Schema::create('comments', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
        //     $table->foreignId('artwork_id')->constrained('images')->onDelete('cascade');
        //     $table->text('content');
        //     $table->timestamps();
        // });

        Schema::create('chat_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
            $table->unique(['user_id', 'artist_id']);
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('chat_rooms')->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->text('content');
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('artist_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->integer('amount');
            $table->enum('status', ['pending', 'completed', 'refunded']);
            $table->timestamps();
        });

        Schema::create('likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('artwork_id')->constrained('images')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        
        Schema::dropIfExists('likes');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('chats');
        Schema::dropIfExists('chat_rooms');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('artwork_categories');
        Schema::dropIfExists('categories');
        //Schema::dropIfExists('artworks');
        Schema::dropIfExists('users');
    }
};
