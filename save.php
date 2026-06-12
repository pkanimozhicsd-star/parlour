<?php
header('Content-Type: application/json');

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';
$allowedOrigins = [
    'https://pkanimozhicsd-star.github.io',
    'http://localhost:8888',
    'http://127.0.0.1:8888'
];
$originAllowed = $origin && (
    in_array($origin, $allowedOrigins, true)
    || preg_match('/^https:\/\/[a-z0-9.-]+\.(infinityfreeapp\.com|free\.nf)$/', $origin)
);
if ($originAllowed) {
    header('Access-Control-Allow-Origin: ' . $origin);
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
    header('Vary: Origin');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!is_array($input)) {
    $input = $_POST;
}

$name = trim($input['name'] ?? '');
$mobile = trim($input['mobile'] ?? '');
$email = trim($input['email'] ?? '');
$service = trim($input['service'] ?? '');

if ($name === '' || $mobile === '' || $email === '' || $service === '') {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
}

if (!preg_match('/^[0-9]{10}$/', $mobile)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Mobile number must be 10 digits']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email id']);
    exit;
}

$file = __DIR__ . '/ResponseFile.json';

$responses = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    $decoded = json_decode($content, true);
    if (is_array($decoded)) {
        $responses = $decoded;
    }
}

$responses[] = [
    'submitted_at' => date('d/m/Y, H:i:s'),
    'name' => $name,
    'mobile' => $mobile,
    'email' => $email,
    'service' => $service
];

if (file_put_contents($file, json_encode($responses, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) === false) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Could not write to ResponseFile.json']);
    exit;
}

echo json_encode([
    'success' => true,
    'message' => 'Saved to ResponseFile.json',
    'file' => 'ResponseFile.json'
]);
