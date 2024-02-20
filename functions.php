<?php
// Function to fetch data from the API
function fetchDataFromAPI($api_url, $headers) {
    $data = file_get_contents($api_url, false, stream_context_create(array(
        'http' => array(
            'method' => 'GET',
            'header' => implode("\r\n", array_map(
                function ($key, $value) {
                    return "$key: $value";
                },
                array_keys($headers),
                $headers
            ))
        )
    )));
    return $data;
}

// Function to fetch data, either from cache or API
function fetchData($api_url, $headers) {
    $data = fetchDataFromAPI($api_url, $headers);
    file_put_contents('cache.json', $data); // Update cache file
}
?>
