<?php
require_once '../config.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Get action from request
$action = $_GET['action'] ?? '';

// Validate action
if (empty($action)) {
    sendErrorResponse('No action specified');
}

switch($action) {
    case 'get_questions':
        // Validate parameters
        if (!isset($_GET['category']) || !isset($_GET['difficulty'])) {
            sendErrorResponse('Missing required parameters');
        }
        
        $category = sanitizeInput($_GET['category']);
        $difficulty = sanitizeInput($_GET['difficulty']);
        
        // Validate difficulty
        if (!in_array($difficulty, ['easy', 'medium', 'hard'])) {
            sendErrorResponse('Invalid difficulty level');
        }
        
        try {
            $conn = getDBConnection();
            
            // Get questions with proper randomization
            $stmt = $conn->prepare("
                SELECT * FROM questions 
                WHERE category = ? AND difficulty = ? 
                ORDER BY RAND() 
                LIMIT 20
            ");
            $stmt->execute([$category, $difficulty]);
            $questions = $stmt->fetchAll();
            
            if (empty($questions)) {
                sendErrorResponse('No questions found for the selected category and difficulty');
            }
            
            sendSuccessResponse(['questions' => $questions]);
        } catch(PDOException $e) {
            error_log("Database error in get_questions: " . $e->getMessage());
            sendErrorResponse('Database error occurred');
        }
        break;
        
    case 'save_score':
        // Check if user is logged in
        if (!isLoggedIn()) {
            sendErrorResponse('User not logged in', 401);
        }
        
        // Get and validate POST data
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            sendErrorResponse('Invalid JSON data');
        }
        
        $requiredFields = ['category', 'difficulty', 'score', 'total_questions', 'time_taken'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field])) {
                sendErrorResponse("Missing required field: $field");
            }
        }
        
        // Validate data types
        if (!is_numeric($data['score']) || !is_numeric($data['total_questions']) || !is_numeric($data['time_taken'])) {
            sendErrorResponse('Invalid data types');
        }
        
        try {
            $conn = getDBConnection();
            
            // Insert score with proper validation
            $stmt = $conn->prepare("
                INSERT INTO scores (
                    user_id, category, difficulty, score, 
                    total_questions, time_taken
                ) VALUES (?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                getCurrentUserId(),
                sanitizeInput($data['category']),
                sanitizeInput($data['difficulty']),
                (int)$data['score'],
                (int)$data['total_questions'],
                (int)$data['time_taken']
            ]);
            
            sendSuccessResponse(['message' => 'Score saved successfully']);
        } catch(PDOException $e) {
            error_log("Database error in save_score: " . $e->getMessage());
            sendErrorResponse('Failed to save score');
        }
        break;
        
    default:
        sendErrorResponse('Invalid action specified');
        break;
}
?> 