<?php

namespace Database\Seeders;

use App\Models\Contact;
use App\Models\Segment;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
    private array $firstNames = [
        'Emma', 'Liam', 'Olivia', 'Noah', 'Ava', 'William', 'Sophia', 'James', 'Isabella', 'Oliver',
        'Mia', 'Benjamin', 'Charlotte', 'Elijah', 'Amelia', 'Lucas', 'Harper', 'Mason', 'Evelyn', 'Logan',
        'Abigail', 'Alexander', 'Emily', 'Ethan', 'Elizabeth', 'Daniel', 'Mila', 'Jackson', 'Ella', 'Sebastian',
        'Avery', 'Aiden', 'Sofia', 'Matthew', 'Camila', 'Henry', 'Aria', 'Michael', 'Scarlett', 'Owen',
    ];

    private array $lastNames = [
        'Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
        'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin',
        'Lee', 'Perez', 'Thompson', 'White', 'Harris', 'Sanchez', 'Clark', 'Ramirez', 'Lewis', 'Robinson',
    ];

    private array $locations = [
        'New York, US', 'Los Angeles, US', 'Chicago, US', 'Houston, US', 'Phoenix, US',
        'London, UK', 'Manchester, UK', 'Birmingham, UK', 'Glasgow, UK', 'Leeds, UK',
        'Toronto, CA', 'Vancouver, CA', 'Montreal, CA', 'Calgary, CA', 'Ottawa, CA',
        'Sydney, AU', 'Melbourne, AU', 'Brisbane, AU', 'Perth, AU', 'Adelaide, AU',
        'Berlin, DE', 'Munich, DE', 'Hamburg, DE', 'Frankfurt, DE', 'Cologne, DE',
        'Paris, FR', 'Lyon, FR', 'Marseille, FR', 'Tokyo, JP', 'Osaka, JP',
    ];

    private array $domains = ['gmail.com', 'yahoo.com', 'hotmail.com', 'outlook.com', 'icloud.com', 'company.com', 'work.io'];
    private array $genders = ['male', 'female', 'other', 'prefer_not_to_say'];
    private array $statuses = ['active', 'active', 'active', 'active', 'active', 'active', 'unsubscribed', 'bounced'];

    public function run(): void
    {
        $adminUser = User::where('role', 'admin')->first();
        $segments  = Segment::all();

        $contacts = [];
        $now      = now();

        for ($i = 0; $i < 500; $i++) {
            $firstName = $this->firstNames[array_rand($this->firstNames)];
            $lastName  = $this->lastNames[array_rand($this->lastNames)];
            $domain    = $this->domains[array_rand($this->domains)];
            $email     = strtolower($firstName . '.' . $lastName . ($i > 30 ? $i : '') . '@' . $domain);

            $contacts[] = [
                'user_id'          => $adminUser->id,
                'email'            => $email,
                'first_name'       => $firstName,
                'last_name'        => $lastName,
                'phone'            => '+1' . rand(200, 999) . rand(1000000, 9999999),
                'location'         => $this->locations[array_rand($this->locations)],
                'age'              => rand(18, 65),
                'gender'           => $this->genders[array_rand($this->genders)],
                'status'           => $this->statuses[array_rand($this->statuses)],
                'custom_fields'    => json_encode(['source' => 'web', 'plan' => ['free', 'pro', 'enterprise'][rand(0, 2)]]),
                'last_activity_at' => $now->copy()->subDays(rand(0, 180))->toDateTimeString(),
                'created_at'       => $now->copy()->subDays(rand(0, 365))->toDateTimeString(),
                'updated_at'       => $now->toDateTimeString(),
            ];
        }

        // Chunk insert for performance
        foreach (array_chunk($contacts, 100) as $chunk) {
            DB::table('contacts')->insertOrIgnore($chunk);
        }

        // Assign contacts to segments
        if ($segments->isNotEmpty()) {
            $contactIds = DB::table('contacts')->pluck('id')->toArray();
            shuffle($contactIds);

            foreach ($segments as $index => $segment) {
                $slice  = array_slice($contactIds, $index * 50, rand(30, 80));
                $pivots = array_map(fn($id) => [
                    'segment_id' => $segment->id,
                    'contact_id' => $id,
                    'added_at'   => now(),
                ], $slice);

                if (!empty($pivots)) {
                    DB::table('segment_contacts')->insertOrIgnore($pivots);
                }

                $segment->update(['contact_count' => count($slice)]);
            }
        }

        $this->command->info('500 contacts seeded.');
    }
}
