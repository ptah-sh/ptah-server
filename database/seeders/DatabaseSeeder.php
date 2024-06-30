<?php

namespace Database\Seeders;

use App\Models\Node;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->withPersonalTeam()->create();

        $user = User::factory()->withPersonalTeam()->create([
            'name' => 'Seeded User',
            'email' => 'seed@example.com',
            'password' => 'password',
        ]);

        Node::factory()->createQuietly([
            'name' => 'Seeded Node',
            'agent_token' => 'seeded-node',
            'team_id' => $user->personalTeam()->id,
        ]);
    }
}
