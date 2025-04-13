<?php
session_start();
require 'db_connection.php';

// Add this sanitization function at the top
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Check member authentication
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'member') {
    header("Location: login.php");
    exit();
}

// Get member details
try {
    $stmt = $pdo->prepare("SELECT * FROM gero WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $member = $stmt->fetch();

    $dependentsStmt = $pdo->prepare("SELECT * FROM dependants WHERE user_id = ?");
    $dependentsStmt->execute([$_SESSION['user_id']]);
    $dependents = $dependentsStmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Member Dashboard</title>
</head>
<body>
    <h1>Welcome, <?= htmlspecialchars($member['full_name']) ?></h1>
    
    <h2>Your Profile</h2>
    <p>Email: <?= htmlspecialchars($member['email']) ?></p>
    <p>Phone: <?= htmlspecialchars($member['phone']) ?></p>
    <p>Address: <?= htmlspecialchars($member['address']) ?></p>

    <h2>Your Dependents</h2>
    <?php if(count($dependents) > 0): ?>
        <ul>
            <?php foreach ($dependents as $dependent): ?>
                <li><?= htmlspecialchars($dependent['full_name']) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>No dependents registered</p>
    <?php endif; ?>

    <a href="logout.php">Logout</a>
</body>
</html>