<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

// Database Connection
$servername = 'localhost';
$user = 'u810412252_nefty';
$password = 'RqmB&Bt?W7d$$$$$$$$$$$$$$$ttgcscdrvr';
$database = 'u810412252_nefty';

$conn = mysqli_connect($servername, $user, $password, $database);
if (!$conn) {
    echo json_encode(['status' => 'error', 'message' => 'Cannot connect to server']);
    exit();
}

// Function to print table contents - This is for debugging and should be removed in production
function printTableContents($conn) {
    $query = "SELECT * FROM hydromatic_users";
    $result = mysqli_query($conn, $query);
    while ($row = mysqli_fetch_assoc($result)) {
        echo "User ID: " . $row['id'] . ", Username: " . $row['username'] . "<br>";
    }
}

// Create Table for hydromatic site if it does not exist
$createTableQuery = "CREATE TABLE IF NOT EXISTS hydromatic_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)";

if (!mysqli_query($conn, $createTableQuery)) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to create or check hydromatic_users table.']);
    exit();
}

// Handling AJAX request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'];
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    if (empty($username) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Both username and password are required']);
        exit();
    }

    if ($action === 'login') {
        $query = "SELECT * FROM hydromatic_users WHERE username = '$username'";
        $result = mysqli_query($conn, $query);
        $row = mysqli_fetch_assoc($result);

        if ($row && password_verify($password, $row['password'])) {
            echo json_encode(['status' => 'success', 'message' => 'Authentication successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }
    } elseif ($action === 'register') {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $query = "INSERT INTO hydromatic_users (username, password) VALUES ('$username', '$hashedPassword')";
        if (mysqli_query($conn, $query)) {
            echo json_encode(['status' => 'success', 'message' => 'Registration successful']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to register. Username might already exist.']);
        }
    }
}
?>

