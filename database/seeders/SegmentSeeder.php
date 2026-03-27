<?php

namespace Database\Seeders;

use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;

class SegmentSeeder extends Seeder
{
    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();

        $segments = [
            [
                'name'          => 'US Active Users',
                'description'   => 'All active contacts located in the United States',
                'filters'       => [
                    ['field' => 'location', 'operator' => 'contains', 'value' => 'US'],
                    ['field' => 'status',   'operator' => 'equals',   'value' => 'active'],
                ],
                'is_dynamic'    => true,
                'contact_count' => 0,
            ],
            [
                'name'          => 'Young Professionals (18-35)',
                'description'   => 'Active contacts aged 18-35',
                'filters'       => [
                    ['field' => 'age',    'operator' => 'greater_than', 'value' => '17'],
                    ['field' => 'age',    'operator' => 'less_than',    'value' => '36'],
                    ['field' => 'status', 'operator' => 'equals',       'value' => 'active'],
                ],
                'is_dynamic'    => true,
                'contact_count' => 0,
            ],
            [
                'name'          => 'UK & Australia Customers',
                'description'   => 'Contacts from the United Kingdom and Australia',
                'filters'       => [
                    ['field' => 'location', 'operator' => 'contains', 'value' => 'UK'],
                ],
                'is_dynamic'    => true,
                'contact_count' => 0,
            ],
            [
                'name'          => 'Female Subscribers',
                'description'   => 'Active female subscribers',
                'filters'       => [
                    ['field' => 'gender', 'operator' => 'equals', 'value' => 'female'],
                    ['field' => 'status', 'operator' => 'equals', 'value' => 'active'],
                ],
                'is_dynamic'    => true,
                'contact_count' => 0,
            ],
            [
                'name'          => 'VIP Enterprise Customers',
                'description'   => 'High-value enterprise plan contacts',
                'filters'       => [],
                'is_dynamic'    => false,
                'contact_count' => 0,
            ],
        ];

        foreach ($segments as $data) {
            Segment::create(array_merge($data, ['user_id' => $adminUser->id]));
        }

        $this->command->info('5 segments seeded.');
    }
}
