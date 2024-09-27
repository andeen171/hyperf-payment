<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetimes();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('document')->unique();
            $table->string('password_hash');
            $table->string('salt');
            $table->enum('type', ['common', 'shopkeeper']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
