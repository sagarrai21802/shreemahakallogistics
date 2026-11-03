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
                'reference' => 'TRQ-2026-0142', 'customer_name' => 'Rakesh Sharma', 'customer_phone' => '+91 98260 41022', 'customer_email' => 'rakesh.sharma@example.test',
                'pickup_address' => 'Scheme 54, Vijay Nagar', 'pickup_city' => 'Indore',
                'destination_address' => 'Transport Nagar', 'destination_city' => 'Indore',
                'goods_category' => 'Furniture', 'goods_description' => 'Two-seater sofa and a dining table, wrapped',
                'quantity' => '2 items', 'weight_kg' => 450, 'is_fragile' => false, 'needs_loading_help' => true,
                'special_instructions' => 'Call before arrival; ground floor pickup.',
                'status' => 'new', 'submitted_at' => $now->copy()->subMinutes(12),
            ],
            [
                'reference' => 'TRQ-2026-0143', 'customer_name' => 'Neha Joshi', 'customer_phone' => '+91 98930 22147', 'customer_email' => null,
                'pickup_address' => 'Kolar Road', 'pickup_city' => 'Bhopal',
                'destination_address' => 'Palasia', 'destination_city' => 'Indore',
                'goods_category' => 'Household Items', 'goods_description' => 'Household shifting: cartons and one refrigerator',
                'quantity' => '14 cartons', 'weight_kg' => 320, 'is_fragile' => false, 'needs_loading_help' => true,
                'special_instructions' => 'Refrigerator must stay upright.',
                'status' => 'new', 'submitted_at' => $now->copy()->subMinutes(35),
            ],
            [
                'reference' => 'TRQ-2026-0144', 'customer_name' => 'Amit Verma', 'customer_phone' => '+91 90090 77412', 'customer_email' => null,
                'pickup_address' => 'Freeganj', 'pickup_city' => 'Ujjain',
                'destination_address' => 'Hadapsar', 'destination_city' => 'Pune',
                'goods_category' => 'Electronics', 'goods_description' => 'LED televisions in original packaging',
                'quantity' => '6 boxes', 'weight_kg' => 180, 'is_fragile' => true, 'needs_loading_help' => false,
                'special_instructions' => 'Fragile; keep upright and away from moisture.',
                'status' => 'new', 'submitted_at' => $now->copy()->subMinutes(70),
            ],
            [
                'reference' => 'TRQ-2026-0145', 'customer_name' => 'Sunrise Interiors', 'customer_phone' => '+91 731 405 1180', 'customer_email' => 'ops@sunriseinteriors.test',
                'pickup_address' => 'Industrial Area', 'pickup_city' => 'Dewas',
                'destination_address' => 'Bhiwandi', 'destination_city' => 'Mumbai',
                'goods_category' => 'Construction Materials', 'goods_description' => 'Tiles and sanitaryware, palletised',
                'quantity' => '22 pallets', 'weight_kg' => 1200, 'is_fragile' => true, 'needs_loading_help' => true,
                'special_instructions' => 'Loading help arranged at both ends.',
                'status' => 'awaiting_responses', 'submitted_at' => $now->copy()->subHours(2),
            ],
            [
                'reference' => 'TRQ-2026-0146', 'customer_name' => 'Kavita Rao', 'customer_phone' => '+91 94250 66381', 'customer_email' => null,
                'pickup_address' => 'Rau', 'pickup_city' => 'Indore',
                'destination_address' => 'Adajan', 'destination_city' => 'Surat',
                'goods_category' => 'Packages', 'goods_description' => 'E-commerce cartons, stacked',
                'quantity' => '40 cartons', 'weight_kg' => 260, 'is_fragile' => false, 'needs_loading_help' => false,
                'special_instructions' => null,
                'status' => 'awaiting_responses', 'submitted_at' => $now->copy()->subMinutes(160),
            ],
            [
                'reference' => 'TRQ-2026-0147', 'customer_name' => 'Shree Balaji Traders', 'customer_phone' => '+91 98270 33015', 'customer_email' => null,
                'pickup_address' => 'Kamptee Road', 'pickup_city' => 'Nagpur',
                'destination_address' => 'Kukatpally', 'destination_city' => 'Hyderabad',
                'goods_category' => 'Furniture', 'goods_description' => 'Office chairs and workstations',
                'quantity' => '18 units', 'weight_kg' => 800, 'is_fragile' => false, 'needs_loading_help' => true,
                'special_instructions' => 'Delivery needs a goods lift.',
                'status' => 'follow_up_needed', 'submitted_at' => $now->copy()->subHours(3),
            ],
            [
                'reference' => 'TRQ-2026-0148', 'customer_name' => 'Prakash Industries', 'customer_phone' => '+91 755 492 2210', 'customer_email' => 'dispatch@prakashind.test',
                'pickup_address' => 'Govindpura', 'pickup_city' => 'Bhopal',
                'destination_address' => 'Vishwakarma Industrial Area', 'destination_city' => 'Jaipur',
                'goods_category' => 'Other', 'goods_description' => 'Industrial pump and fittings',
                'quantity' => '3 crates', 'weight_kg' => 950, 'is_fragile' => false, 'needs_loading_help' => true,
                'special_instructions' => 'Crane available at pickup.',
                'status' => 'follow_up_needed', 'submitted_at' => $now->copy()->subMinutes(200),
            ],
            [
                'reference' => 'TRQ-2026-0149', 'customer_name' => 'Metro Digital', 'customer_phone' => '+91 97130 55428', 'customer_email' => null,
                'pickup_address' => 'Sapna Sangeeta', 'pickup_city' => 'Indore',
                'destination_address' => 'Prahlad Nagar', 'destination_city' => 'Ahmedabad',
                'goods_category' => 'Electronics', 'goods_description' => 'Networking equipment in sealed cartons',
                'quantity' => '9 boxes', 'weight_kg' => 540, 'is_fragile' => true, 'needs_loading_help' => false,
                'special_instructions' => null,
                'status' => 'confirmed', 'submitted_at' => $now->copy()->subHours(26),
            ],
            [
                'reference' => 'TRQ-2026-0150', 'customer_name' => 'Anjali Deshmukh', 'customer_phone' => '+91 99260 88104', 'customer_email' => null,
                'pickup_address' => 'Dewas Gate', 'pickup_city' => 'Ujjain',
                'destination_address' => 'MP Nagar', 'destination_city' => 'Bhopal',
                'goods_category' => 'Packages', 'goods_description' => 'Documents and small cartons',
                'quantity' => '5 cartons', 'weight_kg' => 120, 'is_fragile' => false, 'needs_loading_help' => false,
                'special_instructions' => null,
                'status' => 'closed', 'submitted_at' => $now->copy()->subDays(2),
            ],
        ])->map(fn (array $row) => TransportRequest::query()->create($row))->keyBy('reference');

        $invitations = [
            ['request' => 'TRQ-2026-0145', 'driver' => 'DRV-1042', 'status' => 'pending', 'sent' => 120, 'responded' => null],
            ['request' => 'TRQ-2026-0145', 'driver' => 'DRV-1043', 'status' => 'pending', 'sent' => 120, 'responded' => null],
            ['request' => 'TRQ-2026-0145', 'driver' => 'DRV-1045', 'status' => 'pending', 'sent' => 118, 'responded' => null],
            ['request' => 'TRQ-2026-0145', 'driver' => 'DRV-1046', 'status' => 'pending', 'sent' => 116, 'responded' => null],
            ['request' => 'TRQ-2026-0145', 'driver' => 'DRV-1048', 'status' => 'pending', 'sent' => 115, 'responded' => null],
            ['request' => 'TRQ-2026-0146', 'driver' => 'DRV-1042', 'status' => 'rejected', 'sent' => 140, 'responded' => 125],
            ['request' => 'TRQ-2026-0146', 'driver' => 'DRV-1044', 'status' => 'pending', 'sent' => 140, 'responded' => null],
            ['request' => 'TRQ-2026-0146', 'driver' => 'DRV-1047', 'status' => 'rejected', 'sent' => 135, 'responded' => 110],
            ['request' => 'TRQ-2026-0147', 'driver' => 'DRV-1045', 'status' => 'accepted', 'sent' => 175, 'responded' => 90],
            ['request' => 'TRQ-2026-0147', 'driver' => 'DRV-1046', 'status' => 'rejected', 'sent' => 175, 'responded' => 170],
            ['request' => 'TRQ-2026-0147', 'driver' => 'DRV-1048', 'status' => 'expired', 'sent' => 170, 'responded' => null],
            ['request' => 'TRQ-2026-0147', 'driver' => 'DRV-1049', 'status' => 'rejected', 'sent' => 165, 'responded' => 150],
            ['request' => 'TRQ-2026-0148', 'driver' => 'DRV-1043', 'status' => 'accepted', 'sent' => 160, 'responded' => 60],
            ['request' => 'TRQ-2026-0148', 'driver' => 'DRV-1047', 'status' => 'expired', 'sent' => 155, 'responded' => null],
            ['request' => 'TRQ-2026-0148', 'driver' => 'DRV-1050', 'status' => 'rejected', 'sent' => 150, 'responded' => 130],
            ['request' => 'TRQ-2026-0149', 'driver' => 'DRV-1044', 'status' => 'accepted', 'sent' => 1500, 'responded' => 1200],
            ['request' => 'TRQ-2026-0149', 'driver' => 'DRV-1049', 'status' => 'rejected', 'sent' => 1480, 'responded' => 1440],
            ['request' => 'TRQ-2026-0150', 'driver' => 'DRV-1042', 'status' => 'rejected', 'sent' => 2900, 'responded' => 2870],
            ['request' => 'TRQ-2026-0150', 'driver' => 'DRV-1048', 'status' => 'expired', 'sent' => 2880, 'responded' => null],
        ];

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
            ['type' => 'request_received', 'title' => 'New request received', 'description' => 'TRQ-2026-0142 · Vijay Nagar → Transport Nagar', 'occurred_at' => $now->copy()->subMinutes(12)],
            ['type' => 'request_received', 'title' => 'New request received', 'description' => 'TRQ-2026-0143 · Bhopal → Indore', 'occurred_at' => $now->copy()->subMinutes(35)],
            ['type' => 'application_submitted', 'title' => 'Driver application submitted', 'description' => 'Ravi Chauhan · DRV-2081', 'occurred_at' => $now->copy()->subMinutes(40)],
            ['type' => 'request_received', 'title' => 'New request received', 'description' => 'TRQ-2026-0144 · Ujjain → Pune', 'occurred_at' => $now->copy()->subMinutes(70)],
            ['type' => 'driver_accepted', 'title' => 'Driver accepted an invitation', 'description' => 'Deepak Verma accepted TRQ-2026-0147', 'occurred_at' => $now->copy()->subMinutes(90)],
            ['type' => 'driver_rejected', 'title' => 'Driver declined an invitation', 'description' => 'Arjun Mehta declined TRQ-2026-0146', 'occurred_at' => $now->copy()->subMinutes(110)],
            ['type' => 'invitation_sent', 'title' => 'Invitations sent', 'description' => '5 drivers invited to TRQ-2026-0145', 'occurred_at' => $now->copy()->subMinutes(120)],
            ['type' => 'application_submitted', 'title' => 'Driver application submitted', 'description' => 'Mohan Lal · DRV-2082', 'occurred_at' => $now->copy()->subMinutes(130)],
        ];

        foreach ($events as $event) {
            ActivityEvent::query()->create($event);
        }
    }
}
