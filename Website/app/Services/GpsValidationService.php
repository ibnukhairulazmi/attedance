<?php

namespace App\Services;

class GpsValidationService
{
    private const EARTH_RADIUS_M = 6371000;
    public function calculateDistance(
        float $lat1,
        float $lng1,
        float $lat2,
        float $lng2
    ): int {
        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLng / 2) ** 2;

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return (int) round(self::EARTH_RADIUS_M * $c);
    }


    public function isWithinRadius(
        float $schoolLat,
        float $schoolLng,
        float $studentLat,
        float $studentLng,
        int $maxDistanceMeters
    ): array {

        $distance = $this->calculateDistance(
            $schoolLat,
            $schoolLng,
            $studentLat,
            $studentLng
        );

        return [
            'valid' => $distance <= $maxDistanceMeters,
            'distance' => $distance,
        ];
    }


    public function isValidCoordinate(float $lat, float $lng): bool
    {
        return $lat >= -90 && $lat <= 90 && $lng >= -180 && $lng <= 180;
    }
}
