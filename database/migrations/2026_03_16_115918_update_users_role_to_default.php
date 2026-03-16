<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
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
        // Update existing users to have 'user' role
        User::whereNull('role')->update(['role' => 'user']);

        // Create super admin user if not exists
        if (!User::where('email', 'admin@lovedoc.com')->exists()) {
            User::create([
                'username' => 'admin',
                'name' => 'Super Admin',
                'email' => 'admin@lovedoc.com',
                'password' => bcrypt('admin123'),
                'role' => 'super_admin',
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete super admin user
        User::where('email', 'admin@lovedoc.com')->delete();

        // Reset role to null for existing users
        User::where('role', 'user')->update(['role' => null]);
    }
};
