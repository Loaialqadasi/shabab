<?php 
ini_set('display_errors', 1); 
ini_set('display_startup_errors', 1); 
error_reporting(E_ALL); 

require_once 'config.php'; 

header('Content-Type: application/json'); 

if ($_SERVER['REQUEST_METHOD'] === 'POST') { 
    $input = json_decode(file_get_contents('php://input'), true); 
    $message = trim($input['message'] ?? ''); 

    if (empty($message)) { 
        echo json_encode(['error' => 'No message provided.']); 
        exit; 
    } 

    $data = [ 
        'model' => 'gpt-3.5-turbo', 
        'messages' => [ 
            ['role' => 'user', 'content' => $message] 
        ] 
    ]; 

    $ch = curl_init(AI_API_ENDPOINT); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_POST, true); 
    curl_setopt($ch, CURLOPT_HTTPHEADER, [ 
        'Content-Type: application/json', 
        'Authorization: Bearer ' . AI_API_KEY 
    ]); 
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); 

    $response = curl_exec($ch); 
    $err = curl_error($ch); 
    curl_close($ch); 

    if ($err) { 
        echo json_encode(['error' => 'CURL error: ' . $err]); 
        exit; 
    } 

    $result = json_decode($response, true); 

    // Handle DeepInfra or OpenAI API error format
    if (!$result || isset($result['error'])) {
        $errorMessage = $result['error']['message'] ?? 'Unknown API error.';
        echo json_encode(['error' => 'API Error: ' . $errorMessage]); 
        exit; 
    } 

    $reply = $result['choices'][0]['message']['content'] ?? 'Sorry, I could not process your request.';
    echo json_encode(['reply' => $reply]); 
    exit; 
} 

echo json_encode(['error' => 'Invalid request.']); 
exit; 
?>
