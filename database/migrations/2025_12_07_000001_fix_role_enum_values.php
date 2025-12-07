<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
  /**
   * Run the migrations.
   */
  public function up(): void
  {
    // Update existing data first - convert lowercase to capitalized
    DB::statement("UPDATE users SET role = 'User' WHERE role = 'user'");
    DB::statement("UPDATE users SET role = 'Admin' WHERE role = 'admin'");

    // Now modify column to accept all values
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('User', 'Editor', 'Admin') NOT NULL DEFAULT 'User'");
  }

  /**
   * Reverse the migrations.
   */
  public function down(): void
  {
    DB::statement("UPDATE users SET role = 'user' WHERE role = 'User'");
    DB::statement("UPDATE users SET role = 'user' WHERE role = 'Editor'");
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('user','admin') NOT NULL DEFAULT 'user'");
  }
};
