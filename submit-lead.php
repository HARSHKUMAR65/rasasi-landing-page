<?php
declare(strict_types=1);

header('Content-Type: application/json');

require_once __DIR__ . DIRECTORY_SEPARATOR . 'zoho-crm.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'message' => 'Method not allowed.']);
    exit;
}

$name = trim((string)($_POST['name'] ?? ''));
$company = trim((string)($_POST['company'] ?? ''));
$countryCode = trim((string)($_POST['countryCode'] ?? ''));
$phone = trim((string)($_POST['phone'] ?? ''));
$email = trim((string)($_POST['email'] ?? ''));
$service = trim((string)($_POST['service'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));

if (
    !preg_match('/^[A-Za-z ]{3,}$/', $name)
    || !preg_match('/^[A-Za-z ]{3,}$/', $company)
    || !preg_match('/^[0-9]{7,15}$/', $phone)
    || !filter_var($email, FILTER_VALIDATE_EMAIL)
    || strlen($message) > 500
) {
    http_response_code(422);
    echo json_encode(['ok' => false, 'message' => 'Please provide valid name, company, phone, email, and message details.']);
    exit;
}

$lead = [
    'created_at' => gmdate('c'),
    'name' => $name,
    'company' => $company,
    'country_code' => $countryCode,
    'phone' => $phone,
    'email' => $email,
    'service' => $service,
    'message' => $message,
    'ip' => $_SERVER['REMOTE_ADDR'] ?? '',
    'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
];

$dataDir = __DIR__ . DIRECTORY_SEPARATOR . 'data';
if (!is_dir($dataDir) && !mkdir($dataDir, 0755, true) && !is_dir($dataDir)) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Unable to save enquiry.']);
    exit;
}

$saved = file_put_contents(
    $dataDir . DIRECTORY_SEPARATOR . 'leads.jsonl',
    json_encode($lead, JSON_UNESCAPED_SLASHES) . PHP_EOL,
    FILE_APPEND | LOCK_EX
);

if ($saved === false) {
    http_response_code(500);
    echo json_encode(['ok' => false, 'message' => 'Unable to save enquiry.']);
    exit;
}

$zohoError = null;
try {
    sendLeadToZoho($lead, loadZohoConfig());
} catch (Throwable $error) {
    $zohoError = $error->getMessage();
    file_put_contents(
        $dataDir . DIRECTORY_SEPARATOR . 'zoho-errors.log',
        '[' . gmdate('c') . '] ' . $zohoError . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );
}

if ($zohoError !== null) {
    echo json_encode([
        'ok' => true,
        'message' => 'Thank you! Your enquiry has been received.',
        'zoho_synced' => false,
    ]);
    exit;
}

echo json_encode(['ok' => true, 'message' => 'Thank you! Your enquiry has been received.']);
