<?php
require_once __DIR__ . '/classes/Database.php';

$conn = Database::getConnection();

$first    = 'Admin';
$last     = 'User';
$email    = 'admin@portal.com';
$password = 'Admin@1234';         
$role     = 'admin';

$hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("INSERT INTO users (first, last, email, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$first, $last, $email, $hash, $role]);
    echo "✅ Admin created successfully! Email: $email | Password: $password";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}

?>

