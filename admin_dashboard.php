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

// Check admin authentication
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_POST['user_id'];
    $status = $_POST['status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE gero SET status = ? WHERE user_id = ?");
        $stmt->execute([$status, $user_id]);
        $_SESSION['message'] = "Member status updated successfully";
        header("Location: admin_dashboard.php");
        exit();
    } catch(PDOException $e) {
        $error = "Error updating status: " . $e->getMessage();
    }
}

// Get pending members
try {
    $stmt = $pdo->prepare("SELECT users.user_id, users.username, gero.* 
                         FROM users 
                         JOIN gero ON users.user_id = gero.user_id 
                         WHERE gero.status = 'pending'");
    $stmt->execute();
    $pendingMembers = $stmt->fetchAll();
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    
    <?php if(isset($_SESSION['message'])): ?>
        <p style="color:green"><?= $_SESSION['message'] ?></p>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>
    
    <h2>Pending Approvals</h2>
    <?php foreach ($pendingMembers as $member): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <h3><?= htmlspecialchars($member['full_name']) ?></h3>
            <p>Username: <?= htmlspecialchars($member['username']) ?></p>
            <p>Email: <?= htmlspecialchars($member['email']) ?></p>
            <p>Phone: <?= htmlspecialchars($member['phone']) ?></p>
            <p>Address: <?= htmlspecialchars($member['address']) ?></p>
            
            <form method="POST">
                <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                <button type="submit" name="status" value="approved">Approve</button>
                <button type="submit" name="status" value="rejected">Reject</button>
            </form>
        </div>
    <?php endforeach; ?>

    <h2>Approved Members</h2>
    <?php
    $stmt = $pdo->query("SELECT * FROM gero WHERE status = 'approved'");
    $approvedMembers = $stmt->fetchAll();
    ?>
    <?php foreach ($approvedMembers as $member): ?>
        <div style="border:1px solid #ccc; padding:10px; margin:10px;">
            <h3><?= htmlspecialchars($member['full_name']) ?></h3>
            <p>Phone: <?= htmlspecialchars($member['phone']) ?></p>
            <p>Address: <?= htmlspecialchars($member['address']) ?></p>
        </div>
    <?php endforeach; ?>

    <a href="logout.php">Logout</a>
</body>
</html>