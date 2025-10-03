<?php
// Quick route test script

$base_url = 'http://localhost:8000';
$routes = [
    '/',
    '/admin',
    '/admin/login', 
    '/api/v1/venues',
    '/api/v1/venues/featured',
    '/api/v1/login'
];

echo "🔍 Testing On The Night CMS Routes:\n\n";

foreach ($routes as $route) {
    $url = $base_url . $route;
    $response = get_headers($url, 1);
    
    if ($response) {
        $status = $response[0];
        echo "✅ $url - $status\n";
    } else {
        echo "❌ $url - Failed to connect\n";
    }
}

echo "\n📱 API Test:\n";
$api_response = file_get_contents($base_url . '/api/v1/venues/featured');
if ($api_response) {
    $data = json_decode($api_response, true);
    echo "✅ API Working - Found " . count($data['data']) . " featured venues\n";
} else {
    echo "❌ API failed\n";
}

echo "\n🎯 URLs to try in browser:\n";
echo "• Admin Panel: $base_url/admin\n";
echo "• API Endpoint: $base_url/api/v1/venues/featured\n";
echo "• Live Server: Server is running on $base_url\n";
?>
