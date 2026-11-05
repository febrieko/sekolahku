<?php

namespace App\Services;

use App\Models\SchoolGeofence;

class GeofenceService
{
    /**
     * Earth radius in meters
     */
    protected const EARTH_RADIUS_METERS = 6371000;

    /**
     * Calculate distance between two coordinates using the Haversine formula.
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float Distance in meters
     */
    public function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $latFrom = deg2rad($lat1);
        $lonFrom = deg2rad($lon1);
        $latTo = deg2rad($lat2);
        $lonTo = deg2rad($lon2);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(
            pow(sin($latDelta / 2), 2) +
            cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)
        ));

        return round($angle * self::EARTH_RADIUS_METERS, 2);
    }

    /**
     * Validate user coordinates against all active geofences of the school.
     *
     * @param int $schoolId
     * @param float $latitude
     * @param float $longitude
     * @return array{
     *   is_within_radius: bool,
     *   distance_meters: float,
     *   geofence: SchoolGeofence|null,
     *   allowed_radius: int
     * }
     */
    public function validateLocation(int $schoolId, float $latitude, float $longitude): array
    {
        $geofences = SchoolGeofence::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        if ($geofences->isEmpty()) {
            return [
                'is_within_radius' => true,
                'distance_meters' => 0.0,
                'geofence' => null,
                'allowed_radius' => 100,
            ];
        }

        $minDistance = PHP_FLOAT_MAX;
        $closestGeofence = null;
        $matchedGeofence = null;

        foreach ($geofences as $geofence) {
            $dist = $this->calculateDistance($latitude, $longitude, $geofence->latitude, $geofence->longitude);

            if ($dist < $minDistance) {
                $minDistance = $dist;
                $closestGeofence = $geofence;
            }

            if ($dist <= $geofence->radius_meters) {
                $matchedGeofence = $geofence;
                $minDistance = $dist;
                break;
            }
        }

        if ($matchedGeofence !== null) {
            return [
                'is_within_radius' => true,
                'distance_meters' => $minDistance,
                'geofence' => $matchedGeofence,
                'allowed_radius' => $matchedGeofence->radius_meters,
            ];
        }

        return [
            'is_within_radius' => false,
            'distance_meters' => $minDistance,
            'geofence' => $closestGeofence,
            'allowed_radius' => $closestGeofence ? $closestGeofence->radius_meters : 100,
        ];
    }

    /**
     * Determine attendance status based on check-in time.
     * Default morning cutoff is 07:15:00.
     *
     * @param string $checkInTime Format: H:i:s
     * @param string $lateCutoffTime Format: H:i:s
     * @return string 'present'|'late'
     */
    public function determineStatus(string $checkInTime, string $lateCutoffTime = '07:15:00'): string
    {
        return ($checkInTime > $lateCutoffTime) ? 'late' : 'present';
    }
}
