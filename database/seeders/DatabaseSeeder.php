<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Stripe\Plan;
use Carbon\Carbon;
use Stripe\Stripe;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        Stripe::setApiKey(config('services.stripe.secret'));

        \App\Models\User::factory()->create([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'password' => bcrypt('admin'),
            'email_verified_at' => Carbon::now()->toDateTimeString(),
            'type' => 'admin'
        ]);

        $basic = Plan::create([  
            'product' => [
                'name' => 'Basic'
            ], 
            'amount' => 100*100,
            'currency' => 'php',
            'interval' => 'month',
            'interval_count' => 1
        ]);

        \App\Models\Plan::factory()->create([
            'plan_id' => $basic->id,
            'name' => 'Basic',
            'price' => $basic->amount,
            'billing_period' => $basic->interval,
            'currency' => $basic->currency,
            'interval_count' => $basic->interval_count,
        ]);

        $plus = Plan::create([  
            'product' => [
                'name' => 'Plus'
            ], 
            'amount' => 250*100,
            'currency' => 'php',
            'interval' => 'month',
            'interval_count' => 1
        ]);

        \App\Models\Plan::factory()->create([
            'plan_id' => $plus->id,
            'name' => 'Plus',
            'price' => $plus->amount,
            'billing_period' => $plus->interval,
            'currency' => $plus->currency,
            'interval_count' => $plus->interval_count,
        ]);

        $premium = Plan::create([  
            'product' => [
                'name' => 'Premium'
            ], 
            'amount' => 500*100,
            'currency' => 'php',
            'interval' => 'month',
            'interval_count' => 1
        ]);

        \App\Models\Plan::factory()->create([
            'plan_id' => $premium->id,
            'name' => 'Premium',
            'price' => $premium->amount,
            'billing_period' => $premium->interval,
            'currency' => $premium->currency,
            'interval_count' => $premium->interval_count,
        ]);
    }
}
