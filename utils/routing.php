<?php
// Nearest Neighbor Route Optimization and Distance Calculation Functions

// calculate distance using Haversine formula
// param = latitude and longitude of two points
function calculateDistance($lat1, $lng1, $lat2, $lng2) {
    $earthRadius = 6371;
    
    $dLat = deg2rad($lat2 - $lat1);
    $dLng = deg2rad($lng2 - $lng1);
    
    // haversine formula
    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLng / 2) * sin($dLng / 2);
    
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
    $distance = $earthRadius * $c;
    
    return $distance;
}

// optimization function using Nearest Neighbor algorithm
// greedy algorithm that provides good approximation (70-80% optimal)
// param = current latitude, longitude, and array of destinations
function optimizeRouteNearestNeighbor($currentLat, $currentLng, $destinations) {
    if (empty($destinations)) {
        return [];
    }
    
    if (count($destinations) == 1) {
        $destinations[0]['sequence'] = 1;
        $destinations[0]['distance_from_previous'] = calculateDistance(
            $currentLat, $currentLng,
            $destinations[0]['lat'], $destinations[0]['lng']
        );
        return $destinations;
    }
    
    $visited = [];
    $optimizedRoute = [];
    $current = ['lat' => $currentLat, 'lng' => $currentLng];
    $sequence = 1;
    
    while (count($visited) < count($destinations)) {
        $nearestIndex = null;
        $minDistance = PHP_INT_MAX;
        
        foreach ($destinations as $index => $destination) {

            if (in_array($index, $visited)) {
                continue;
            }
            
            $distance = calculateDistance(
                $current['lat'], $current['lng'],
                $destination['lat'], $destination['lng']
            );
            
            if ($distance < $minDistance) {
                $minDistance = $distance;
                $nearestIndex = $index;
            }
        }
        
        if ($nearestIndex !== null) {
            $visited[] = $nearestIndex;
            
            $destinations[$nearestIndex]['sequence'] = $sequence;
            $destinations[$nearestIndex]['distance_from_previous'] = round($minDistance, 2);
            $destinations[$nearestIndex]['distance_from_start'] = $sequence == 1 
                ? round($minDistance, 2)
                : round(end($optimizedRoute)['distance_from_start'] + $minDistance, 2);
            
            $optimizedRoute[] = $destinations[$nearestIndex];
            
            $current = [
                'lat' => $destinations[$nearestIndex]['lat'],
                'lng' => $destinations[$nearestIndex]['lng']
            ];
            
            $sequence++;
        }
    }
    
    return $optimizedRoute;
}


// calculate route statistics
// param = starting latitude, longitude, and array of destinations
function calculateRouteStatistics($startLat, $startLng, $destinations) {
    if (empty($destinations)) {
        return [
            'total_distance' => 0,
            'total_stops' => 0,
            'average_distance' => 0
        ];
    }
    
    $totalDistance = 0;
    $current = ['lat' => $startLat, 'lng' => $startLng];
    
    foreach ($destinations as $destination) {
        $distance = calculateDistance(
            $current['lat'], $current['lng'],
            $destination['lat'], $destination['lng']
        );
        $totalDistance += $distance;
        $current = $destination;
    }
    
    return [
        'total_distance' => round($totalDistance, 2),
        'total_stops' => count($destinations),
        'average_distance' => round($totalDistance / count($destinations), 2)
    ];
}

// format distance for display
// param = distance in kilometers
function formatDistance($distance) {
    if ($distance < 1) {
        return round($distance * 1000) . ' m';
    }
    return round($distance, 2) . ' km';
}
?>