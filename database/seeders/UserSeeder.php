<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserActivity;
use App\Models\UserPoint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Artisan;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();
        $activityTypes = UserActivity::ACTIVITY_TYPES;

        for ($i = 0; $i < 10; $i++) {
            $user = User::create([
                'full_name' => $faker->name,
            ]);

            $activityCount = rand(5, 15);
            $activityList = [];

            for ($j = 0; $j < $activityCount; $j++) {
                $activityList[] = [
                    'user_id'    => $user->id,
                    'name'       => $faker->randomElement($activityTypes),
                    'date'       => $faker->dateTimeBetween('-30 days', 'now')->format('Y-m-d'),
                    'points'     => 20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }

            UserActivity::insert($activityList);
        }

        Artisan::call('app:recalculate');
    }
}
