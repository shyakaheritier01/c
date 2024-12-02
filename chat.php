<?php
// chat.php

// Database connection
$host = 'localhost';
$dbname = 'shyaka_store';
$username = 'root';  // Use your MySQL username
$password = '';      // Use your MySQL password

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

header('Content-Type: application/json');

// Get the action from the request
$action = $_GET['action'] ?? '';

if ($action == 'getMessages') {
  // Get messages from database
  $sql = "SELECT sender, message FROM messages ORDER BY timestamp DESC LIMIT 10";
  $result = $conn->query($sql);
  $messages = [];
  
  while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
  }

  echo json_encode(['messages' => $messages]);
} else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  // Get posted message
  $inputData = json_decode(file_get_contents('php://input'), true);
  $message = $inputData['message'] ?? '';

  if ($message) {
    $stmt = $conn->prepare("INSERT INTO messages (sender, message) VALUES (?, ?)");
    $stmt->bind_param("ss", $sender = "Client", $message);
    $stmt->execute();
    $stmt->close();
    echo json_encode(['success' => true]);
  } else {
    echo json_encode(['success' => false, 'message' => 'No message provided']);
  }
}

$conn->close();
?>
