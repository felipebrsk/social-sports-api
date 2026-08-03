<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            RoleSeeder::class,
            SportSeeder::class,
            ProviderSeeder::class,
            MediaTypeSeeder::class,
            SkillLevelSeeder::class,
            PaymentTypeSeeder::class,
            PaymentStatusSeeder::class,
            SocialNetworkSeeder::class,
            FeedbackStatusSeeder::class,
            FeedbackCategorySeeder::class,
            ConversationTypeSeeder::class,
            GameSessionStatusSeeder::class,
            GameSessionRequestStatusSeeder::class,
        ]);
    }
}
