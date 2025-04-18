<?php
// --------------------------------------------------
// Critical session handling - MUST be first in file
// --------------------------------------------------
if (session_status() === PHP_SESSION_NONE) {
    // Configure session settings BEFORE starting
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    ini_set('session.sid_length', 128);
    ini_set('session.sid_bits_per_character', 6);
    
    session_start();
} else {
    // Session was already started elsewhere
    error_log('Session configuration skipped - session already active');
}

// --------------------------------------------------
// Error reporting
// --------------------------------------------------
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --------------------------------------------------
// Database configuration
// --------------------------------------------------
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'quiz404');

// --------------------------------------------------
// Database connection function
// --------------------------------------------------
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        return $conn;
    } catch(PDOException $e) {
        error_log("Database connection failed: " . $e->getMessage());
        die("Connection failed. Please try again later.");
    }
}

// --------------------------------------------------
// Authentication functions
// --------------------------------------------------
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null;
}

// --------------------------------------------------
// Security functions
// --------------------------------------------------
function sanitizeInput($data) {
    if (is_array($data)) {
        return array_map('sanitizeInput', $data);
    }
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// --------------------------------------------------
// Response helpers
// --------------------------------------------------
function sendJSONResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function sendErrorResponse($message, $statusCode = 400) {
    sendJSONResponse(['error' => $message], $statusCode);
}

function sendSuccessResponse($data = null) {
    sendJSONResponse(['success' => true, 'data' => $data]);
}

// --------------------------------------------------
// Security footer
// --------------------------------------------------
if (basename($_SERVER['PHP_SELF']) === 'config.php') {
    header('HTTP/1.0 403 Forbidden');
    exit('Direct access not permitted');
}
?>