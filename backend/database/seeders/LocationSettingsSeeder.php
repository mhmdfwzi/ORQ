<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class LocationSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Map Location Coordinates
        Setting::setValue('map_latitude', '30.4664199', 'contact');
        Setting::setValue('map_longitude', '31.1893042', 'contact');
        
        $this->command->info('Location settings saved successfully!');
        $this->command->info('Latitude: 30.4664199');
        $this->command->info('Longitude: 31.1893042');
    }
}
