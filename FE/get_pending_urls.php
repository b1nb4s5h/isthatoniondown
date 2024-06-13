<?php
// API token authentication
$api_token = 'some-token-here';
if (!isset($_SERVER['HTTP_API_TOKEN']) || $_SERVER['HTTP_API_TOKEN']!== $api_token) {
    http_response_code(401);
    echo 'Unauthorized';
    exit;
}

// Create a SQLite database connection
$conn = new SQLite3('search_history.db');
$conn->busyTimeout(5000); // 5 seconds

// Retrieve pending URLs from database
$result = $conn->query("SELECT id, url FROM search_history WHERE status = 'Pending'");

$data = array();
while ($row = $result->fetchArray()) {
    $data[] = array('id' => $row['id'], 'url' => $row['url']);
}

echo json_encode($data);
