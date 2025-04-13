<?php
// hash_password.php - DELETE THIS FILE AFTER USE!

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $plain_password = $_POST['password'];
    $hashed_password = password_hash($plain_password, PASSWORD_DEFAULT);
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Password Hasher</title>
</head>
<body>
    <h2>Password Hashing Tool</h2>
    
    <?php if(isset($hashed_password)): ?>
    <div style="margin: 20px; padding: 10px; background: #f0f0f0;">
        <h3>Hashed Password:</h3>
        <input type="text" value="<?= $hashed_password ?>" style="width: 500px;" readonly>
        <p>Use this in your SQL query!</p>
    </div>
    <?php endif; ?>

    <form method="POST">
        <label>Enter Plain Text Password:</label><br>
        <input type="password" name="password" required><br><br>
        <button type="submit">Generate Hash</button>
    </form>

    <div style="margin-top: 20px; color: red;">
        <strong>Important:</strong> 
        <ul>
            <li>Delete this file after use</li>
            <li>Never expose this publicly</li>
            <li>Use strong passwords</li>
        </ul>
    </div>
</body>
</html>