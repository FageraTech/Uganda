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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT users.*, gero.status 
                              FROM users 
                              JOIN gero ON users.user_id = gero.user_id 
                              WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] === 'approved') {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = 'member';
                header("Location: member_dashboard.php");
                exit();
            } else {
                $error = "Your account is pending approval";
            }
        } else {
            $error = "Invalid credentials";
        }
    } catch(PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Member Login</title>
</head>
<body>
    <?php if(isset($error)): ?>
        <p style="color:red"><?= $error ?></p>
    <?php endif; ?>
    
    <h2>Member Login</h2>
    <form method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
    <p>Not registered? <a href="registration.php">Register here</a></p>
</body>
</html>