<?php

namespace Tests\Unit;

use App\Models\SchoolLocation;
use App\Services\GeofenceService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GeofenceServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculates_haversine_distance_correctly(): void
    {
        $service = new GeofenceService();
        // Distance between Monas (-6.175392, 106.827153) and Gambir (-6.176667, 106.830556) is around 400m
        $distance = $service->calculateDistanceMeters(-6.175392, 106.827153, -6.176667, 106.830556);

        $this->assertGreaterThan(300, $distance);
        $this->assertLessThan(600, $distance);
    }

    public function test_validates_location_within_radius(): void
    {
        SchoolLocation::create([
            'name' => 'Gerbang Utama',
            'latitude' => -6.2000000,
            'longitude' => 106.8166667,
            'radius_meters' => 100,
            'is_active' => true,
        ]);

        $service = new GeofenceService();
        // Exact same location
        $result = $service->checkLocation(-6.2000000, 106.8166667);
        $this->assertTrue($result['is_valid']);
        $this->assertEquals(0, $result['distance_meters']);
    }
}
