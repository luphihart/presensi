<?php

namespace App\Services;

use App\Models\SchoolLocation;

class GeofenceService
{
    /**
     * Calculate Haversine distance between two coordinates in meters.
     */
    public function calculateDistanceMeters(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // Earth's radius in meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return round($earthRadius * $c, 2);
    }

    /**
     * Check if given coordinates are within the radius of any active school location.
     */
    public function checkLocation(float $lat, float $lng): array
    {
        $activeLocations = SchoolLocation::where('is_active', true)->get();

        if ($activeLocations->isEmpty()) {
            // Fallback default radius if no DB location exists
            return [
                'is_valid' => true,
                'distance_meters' => 0.0,
                'location_name' => 'Default Location',
            ];
        }

        $minDistance = null;
        $matchedLocation = null;

        foreach ($activeLocations as $location) {
            $dist = $this->calculateDistanceMeters($lat, $lng, (float)$location->latitude, (float)$location->longitude);
            if ($dist <= $location->radius_meters) {
                return [
                    'is_valid' => true,
                    'distance_meters' => $dist,
                    'location_name' => $location->name,
                ];
            }

            if ($minDistance === null || $dist < $minDistance) {
                $minDistance = $dist;
                $matchedLocation = $location;
            }
        }

        return [
            'is_valid' => false,
            'distance_meters' => $minDistance ?? 9999.0,
            'location_name' => $matchedLocation->name ?? 'Sekolah',
        ];
    }
}
