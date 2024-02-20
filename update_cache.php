<?php
// Cache duration in seconds (30 minutes)
$cache_duration = 1800;

// Fetch data from DefectDojo API
$base_url = 'https://demo.defectdojo.org';
$api_url = $base_url . '/api/v2/tests/?limit=800000';
$headers = array(
    'content-type' => 'application/json',
    'Authorization' => 'Token 4c9d47de2eca88aecf5578b604f4a9319c3b5460'
);

// Fetch data
$data = fetchDataFromAPI($api_url, $headers);

// Save data to cache
file_put_contents('cache.json', $data);

echo 'Cache updated successfully.';
?>
