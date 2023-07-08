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
    Schema::create('fsa_profiles', function (Blueprint $table) {
      $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
      $table->string('first_name');
      $table->string('last_name');
      $table->date('birthday')->nullable();
      $table->enum('gender', ['male', 'female'])->nullable();
      $table->string('address')->nullable();
      $table->string('city')->nullable();
      $table->string('state')->nullable();
      $table->string('postal_code')->nullable();
      $table->char('country', 2)->nullable();
      $table->char('locale', 2)->default('en');
      $table->timestamps();

      $table->primary('user_id');
    });
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    Schema::dropIfExists('fsa_profiles');
  }
};
