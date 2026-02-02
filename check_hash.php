<?php
require_once 'akademik-kampus/akademik-kampus/config/database.php';

$db = new Database();
$conn = $db->connect();

$stmt = $conn->prepare("SELECT password FROM admin WHERE email = 'admin@university.edu' LIMIT 1");
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    echo "Current hash: " . $result['password'] . "\n";
    $test_password = "Password salah!";
    if (password_verify($test_password, $result['password'])) {
        echo "Password matches!\n";
    } else {
        echo "Password does not match!\n";
    }
} else {
    echo "Admin not found!\n";
}
?>
