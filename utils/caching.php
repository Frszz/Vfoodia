<?php
require "../connect.php";

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$startLat = $_GET['startLat'] ?? null;
$startLng = $_GET['startLng'] ?? null;
$endLat = $_GET['endLat'] ?? null;
$endLng = $_GET['endLng'] ?? null;

if(!$startLat || !$startLng || !$endLat || !$endLng) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing required parameters.', 'required' => 'startLat, startLng, endLat, endLng']);
    exit;
}

$startLat = round(floatval($startLat), 5);
$startLng = round(floatval($startLng), 5);
$endLat = round(floatval($endLat), 5);
$endLng = round(floatval($endLng), 5);

$stmt = $con->prepare("
    SELECT route_geometry, distance, duration, instructions, created_at 
    FROM tbl_route_cache 
    WHERE start_lat = ? AND start_lng = ? AND end_lat = ? AND end_lng = ?
    AND created_at > DATE_SUB(NOW(), INTERVAL 7 DAY)
    LIMIT 1
");
$stmt->bind_param("dddd", $startLat, $startLng, $endLat, $endLng);
$stmt->execute();
$result = $stmt->get_result();

if ($cached = $result->fetch_assoc()) {
    $response = [
        'routes' => [[
            'geometry' => $cached['route_geometry'],
            'summary' => [
                'distance' => floatval($cached['distance']),
                'duration' => floatval($cached['duration'])
            ]
        ]],
        'source' => 'cache',
        'cached' => true,
        'cached_at' => $cached['created_at']
    ];
    
    // add instructions if available
    if (!empty($cached['instructions'])) {
        $response['routes'][0]['segments'] = json_decode($cached['instructions'], true);
    }
    
    echo json_encode($response);
    exit;
}

$apiKey = $heigitBASICkey;

if (empty($apiKey)) {
    http_response_code(500);
    echo json_encode(['error' => 'API key not configured']);
    exit;
}

$profile = 'driving-car';

// driving car
$url = "https://api.openrouteservice.org/v2/directions/{$profile}?api_key={$apiKey}";
$requestData = [
    'coordinates' => [
        [(float)$startLng, (float)$startLat],
        [(float)$endLng, (float)$endLat]
    ],
    'instructions' => true,
    'geometry' => true,
    'language' => 'id'
];

$ch = curl_init($url);
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => json_encode($requestData),
    CURLOPT_HTTPHEADER => [
        'Content-Type: application/json',
        'Accept: application/json'
    ],
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 10
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

if ($httpCode == 200 && $response) {
    $json = json_decode($response, true);
    
    if (isset($json['routes'][0])) {
        $route = $json['routes'][0];
        $geometry = $route['geometry'];
        $distance = $route['summary']['distance'];
        $duration = $route['summary']['duration'];
        
        // extract segments/instructions for turn-by-turn navigation
        $instructions = null;
        if (isset($route['segments']) && is_array($route['segments'])) {
            $instructions = json_encode($route['segments']);
        }
        
        $insertStmt = $con->prepare("
            INSERT INTO tbl_route_cache 
            (start_lat, start_lng, end_lat, end_lng, route_geometry, distance, duration, instructions, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
            ON DUPLICATE KEY UPDATE 
                route_geometry = VALUES(route_geometry),
                distance = VALUES(distance),
                duration = VALUES(duration),
                instructions = VALUES(instructions),
                updated_at = NOW()
        ");
        
        $insertStmt->bind_param("ddddsids", 
            $startLat, $startLng, $endLat, $endLng, 
            $geometry, $distance, $duration, $instructions
        );
        
        $insertStmt->execute();
        
        $responseData = [
            'routes' => [[
                'geometry' => $geometry,
                'summary' => [
                    'distance' => $distance,
                    'duration' => $duration
                ]
            ]],
            'cached' => false
        ];
        
        if (isset($route['segments'])) {
            $responseData['routes'][0]['segments'] = $route['segments'];
        }
        
        echo json_encode($responseData);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid API response format']);
    }
} else if ($httpCode == 429) {
    http_response_code(429);
    echo json_encode([
        'error' => 'Rate limit exceeded',
        'retry_after' => 5,
        'message' => 'Too many requests, please try again later'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'error' => 'API request failed',
        'http_code' => $httpCode,
        'curl_error' => $curlError
    ]);
}

?>
