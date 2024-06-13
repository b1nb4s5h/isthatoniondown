<?php
// update_status.php

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

// Get ID and status from request
$id = $_POST['id'];
$status = $_POST['status'];

// Update status
$conn->exec("UPDATE search_history SET status = '$status' WHERE id = $id");

echo 'Status updated successfully';
