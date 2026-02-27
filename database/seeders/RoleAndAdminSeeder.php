<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Client;
use App\Models\Technician;
use App\Models\ServiceJob;
use Spatie\Permission\Models\Role;

class RoleAndAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Create roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $techRole  = Role::firstOrCreate(['name' => 'technician']);

        // Create admin user
        $admin = User::firstOrCreate(
            ['email' => 'admin@fsm.com'],
            [
                'name'     => 'Admin User',
                'password' => bcrypt('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // Create a sample technician user
        $techUser = User::firstOrCreate(
            ['email' => 'tech@fsm.com'],
            [
                'name'     => 'John Technician',
                'password' => bcrypt('password'),
            ]
        );
        $techUser->assignRole($techRole);

        $technician = Technician::firstOrCreate(
            ['user_id' => $techUser->id],
            [
                'phone'          => '+91-9876543210',
                'specialization' => 'Termite Control',
                'license_number' => 'LIC-2024-001',
                'is_active'      => true,
            ]
        );

        // Create sample clients
        $client1 = Client::firstOrCreate(
            ['email' => 'ravi@acmecorp.com'],
            [
                'name'    => 'Ravi Kumar',
                'phone'   => '+91-9812345678',
                'company' => 'Acme Corp',
                'address' => '12 MG Road, Bengaluru, Karnataka',
                'notes'   => 'Prefers morning appointments',
            ]
        );

        $client2 = Client::firstOrCreate(
            ['email' => 'priya@homestyle.com'],
            [
                'name'    => 'Priya Sharma',
                'phone'   => '+91-9898765432',
                'company' => 'Homestyle Interiors',
                'address' => '45 Patel Nagar, New Delhi',
                'notes'   => 'Monthly contract client',
            ]
        );

        // Create sample jobs
        ServiceJob::firstOrCreate(
            ['job_number' => 'JOB-SEED001'],
            [
                'client_id'      => $client1->id,
                'technician_id'  => $technician->id,
                'title'          => 'Annual Termite Inspection',
                'description'    => 'Full property termite inspection and treatment',
                'status'         => 'pending',
                'priority'       => 'high',
                'service_type'   => 'Termite Control',
                'scheduled_at'   => now()->addDays(2),
                'latitude'       => 12.9716,
                'longitude'      => 77.5946,
                'location_address' => '12 MG Road, Bengaluru, Karnataka',
            ]
        );

        ServiceJob::firstOrCreate(
            ['job_number' => 'JOB-SEED002'],
            [
                'client_id'      => $client2->id,
                'technician_id'  => $technician->id,
                'title'          => 'Cockroach Treatment',
                'description'    => 'Kitchen and bathroom cockroach elimination',
                'status'         => 'completed',
                'priority'       => 'medium',
                'service_type'   => 'General Pest Control',
                'scheduled_at'   => now()->subDays(3),
                'completed_at'   => now()->subDays(2),
                'latitude'       => 28.7041,
                'longitude'      => 77.1025,
                'location_address' => '45 Patel Nagar, New Delhi',
            ]
        );
    }
}
