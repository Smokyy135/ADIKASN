<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-admin-user';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create admin user for testing';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Check if admin exists
        $admin = User::where('role', 'admin')->first();
        
        if ($admin) {
            $this->info('Admin user already exists');
            return 0;
        }

        // Create admin user
        User::create([
            'name' => 'Admin ADIKASN',
            'nip' => '199001011234567890',
            'password' => bcrypt('password123'),
            'role' => 'admin',
        ]);

        $this->info('Admin user created successfully!');
        $this->info('Email/NIP: 199001011234567890');
        $this->info('Password: password123');
        
        return 0;
    }
}
