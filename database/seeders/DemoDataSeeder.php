<?php

namespace Database\Seeders;

use App\Models\ActivityEvent;
use App\Models\Driver;
use App\Models\DriverApplication;
use App\Models\DriverInvitation;
use App\Models\TransportRequest;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        if (app()->environment('production')) {
            return;
        }

        DriverInvitation::query()->delete();
        ActivityEvent::query()->delete();
        DriverApplication::query()->delete();
        TransportRequest::query()->delete();
        Driver::query()->delete();

        $now = Carbon::now();

        $drivers = collect([
            // 5 available drivers with real GPS positions around Indore city
            ['partner_ref' => 'DRV-1042', 'name' => 'Suresh Patel', 'phone' => '+91 98263 11001', 'email' => null, 'vehicle_type' => 'Tata 407', 'vehicle_model' => 'Tata 407', 'registration_number' => 'MP09 AB 1234', 'capacity_tons' => 3.00, 'approval_status' => 'approved', 'availability_status' => 'available', 'last_location_at' => $now->copy()->subMinutes(3), 'lat' => 22.7355, 'lng' => 75.8703],
            ['partner_ref' => 'DRV-1043', 'name' => 'Mahesh Yadav', 'phone' => '+91 98932 11002', 'email' => null, 'vehicle_type' => 'Eicher Pro', 'vehicle_model' => 'Eicher Pro 3015', 'registration_number' => 'MP09 CD 5678', 'capacity_tons' => 8.50, 'approval_status' => 'approved', 'availability_status' => 'available', 'last_location_at' => $now->copy()->subMinutes(8), 'lat' => 22.6955, 'lng' => 75.8404],
            ['partner_ref' => 'DRV-1044', 'name' => 'Imran Khan', 'phone' => '+91 90094 11003', 'email' => null, 'vehicle_type' => 'Ashok Leyland Dost', 'vehicle_model' => 'Ashok Leyland Dost', 'registration_number' => 'MP13 EF 9012', 'capacity_tons' => 1.50, 'approval_status' => 'approved', 'availability_status' => 'available', 'last_location_at' => $now->copy()->subMinutes(12), 'lat' => 22.7511, 'lng' => 75.9002],
            ['partner_ref' => 'DRV-1045', 'name' => 'Deepak Verma', 'phone' => '+91 94251 11004', 'email' => null, 'vehicle_type' => 'Tata LPT', 'vehicle_model' => 'Tata LPT 1618', 'registration_number' => 'MP04 GH 3456', 'capacity_tons' => 16.00, 'approval_status' => 'approved', 'availability_status' => 'available', 'last_location_at' => $now->copy()->subMinutes(6), 'lat' => 22.7030, 'lng' => 75.8890],
            ['partner_ref' => 'DRV-1046', 'name' => 'Sunil Rathore', 'phone' => '+91 98271 11005', 'email' => null, 'vehicle_type' => 'Mahindra Blazo', 'vehicle_model' => 'Mahindra Blazo X 28', 'registration_number' => 'MP09 IJ 7890', 'capacity_tons' => 12.00, 'approval_status' => 'approved', 'availability_status' => 'available', 'last_location_at' => $now->copy()->subMinutes(9), 'lat' => 22.7425, 'lng' => 75.8210],
            // Non-available drivers without GPS
            ['partner_ref' => 'DRV-1047', 'name' => 'Arjun Mehta', 'phone' => '+91 97131 11006', 'email' => null, 'vehicle_type' => 'Tata Ace', 'vehicle_model' => 'Tata Ace Gold', 'registration_number' => 'MP13 KL 2345', 'capacity_tons' => 1.00, 'approval_status' => 'approved', 'availability_status' => 'busy', 'last_location_at' => $now->copy()->subMinutes(25)],
            ['partner_ref' => 'DRV-1048', 'name' => 'Vikram Singh', 'phone' => '+91 99261 11007', 'email' => null, 'vehicle_type' => 'Eicher', 'vehicle_model' => 'Eicher 1110', 'registration_number' => 'MP04 MN 6789', 'capacity_tons' => 5.00, 'approval_status' => 'approved', 'availability_status' => 'busy', 'last_location_at' => $now->copy()->subMinutes(40)],
            ['partner_ref' => 'DRV-1049', 'name' => 'Ramesh Choudhary', 'phone' => '+91 98933 11008', 'email' => null, 'vehicle_type' => 'Tata 407', 'vehicle_model' => 'Tata 407', 'registration_number' => 'MP09 OP 1122', 'capacity_tons' => 3.00, 'approval_status' => 'approved', 'availability_status' => 'offline', 'last_location_at' => $now->copy()->subHours(3)],
            ['partner_ref' => 'DRV-1050', 'name' => 'Anil Kumar', 'phone' => '+91 90095 11009', 'email' => null, 'vehicle_type' => 'Ashok Leyland', 'vehicle_model' => 'Ashok Leyland 2820', 'registration_number' => 'MP13 QR 3344', 'capacity_tons' => 20.00, 'approval_status' => 'approved', 'availability_status' => 'offline', 'last_location_at' => $now->copy()->subHours(5)],
            ['partner_ref' => 'DRV-1051', 'name' => 'Faizan Ali', 'phone' => '+91 94252 11010', 'email' => null, 'vehicle_type' => 'Tata Ultra', 'vehicle_model' => 'Tata Ultra 1518', 'registration_number' => 'MP09 ST 5566', 'capacity_tons' => 7.00, 'approval_status' => 'approved', 'availability_status' => 'location_stale', 'last_location_at' => $now->copy()->subMinutes(130)],
        ])->map(fn (array $row) => Driver::query()->create($row))->keyBy('partner_ref');

        $requests = collect([
            [
                'reference' => 'TRQ-2026-0143', 'customer_name' => 'Blinkit Enterprise', 'customer_phone' => '+91 98930 22147', 'customer_email' => null,
                'pickup_address' => 'Kolar Road', 'pickup_city' => 'Bhopal',
                'destination_address' => 'Palasia', 'destination_city' => 'Indore',
                'goods_category' => 'Household Items', 'goods_description' => 'Household shifting: cartons and one refrigerator',
                'quantity' => '14 cartons', 'weight_kg' => 2000, 'is_fragile' => false, 'needs_loading_help' => true,
                'special_instructions' => 'Refrigerator must stay upright.',
                'status' => 'new', 'submitted_at' => $now->copy()->subMinutes(35),
            ],
        ])->map(fn (array $row) => TransportRequest::query()->create($row))->keyBy('reference');

        $invitations = [];

        foreach ($invitations as $invitation) {
            DriverInvitation::query()->create([
                'transport_request_id' => $requests[$invitation['request']]->id,
                'driver_id' => $drivers[$invitation['driver']]->id,
                'response_status' => $invitation['status'],
                'sent_at' => $now->copy()->subMinutes($invitation['sent']),
                'responded_at' => $invitation['responded'] === null ? null : $now->copy()->subMinutes($invitation['responded']),
            ]);
        }

        collect([
            ['driver_name' => 'Ravi Chauhan', 'partner_ref' => 'DRV-2081', 'vehicle_type' => 'Tata 407', 'documents_received' => 6, 'documents_total' => 6, 'status' => 'pending_review', 'submitted_at' => $now->copy()->subMinutes(40)],
            ['driver_name' => 'Mohan Lal', 'partner_ref' => 'DRV-2082', 'vehicle_type' => 'Eicher Pro 3015', 'documents_received' => 4, 'documents_total' => 6, 'status' => 'pending_review', 'submitted_at' => $now->copy()->subHours(2)],
            ['driver_name' => 'Sanjay Gupta', 'partner_ref' => 'DRV-2083', 'vehicle_type' => 'Tata Ace Gold', 'documents_received' => 5, 'documents_total' => 6, 'status' => 'needs_information', 'submitted_at' => $now->copy()->subHours(26)],
            ['driver_name' => 'Pooja Devi', 'partner_ref' => 'DRV-2084', 'vehicle_type' => 'Ashok Leyland Dost', 'documents_received' => 6, 'documents_total' => 6, 'status' => 'approved', 'submitted_at' => $now->copy()->subDays(2)],
        ])->each(fn (array $row) => DriverApplication::query()->create($row));

        $events = [
            ['type' => 'request_received', 'title' => 'New request received', 'description' => 'TRQ-2026-0143 · Bhopal → Indore', 'occurred_at' => $now->copy()->subMinutes(35)],
        ];

        foreach ($events as $event) {
            ActivityEvent::query()->create($event);
        }
    }
}
