<?php
require_once __DIR__ . '/../db.php';

$query = trim($_GET['q'] ?? '');
$results = [];

if (strlen($query) >= 2) {
    $search = "%$query%";
    $stmt = $conn->prepare("SELECT name, slug, price_per_day FROM products WHERE name LIKE ? OR description LIKE ? LIMIT 8");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($results);
