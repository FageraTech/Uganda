<?php
// Database connections
require_once 'db_connection.php';

// Add this sanitization function at the top
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $email = filter_var(sanitizeInput($_POST['email']), FILTER_VALIDATE_EMAIL);
    $full_name = sanitizeInput($_POST['full_name']);
    $phone = preg_match("/^[0-9]{10,15}$/", $_POST['phone']) ? $_POST['phone'] : null;
    $address = sanitizeInput($_POST['address']);

    if (!$email) {
        echo "<p style='color:red;'>Invalid email format.</p>";
    } elseif (!$phone) {
        echo "<p style='color:red;'>Invalid phone number. It must contain 10 to 15 digits.</p>";
    } else {
        try {
            $pdo->beginTransaction();
            
            // Insert into users table
            $stmt = $pdo->prepare("INSERT INTO users (username, password, email) VALUES (?, ?, ?)");
            $stmt->execute([$username, $password, $email]);
            $user_id = $pdo->lastInsertId();
            
            // Insert into gero table
            $stmt = $pdo->prepare("INSERT INTO gero (user_id, full_name, phone, address, registration_date) 
                                 VALUES (?, ?, ?, ?, CURDATE())");
            $stmt->execute([$user_id, $full_name, $phone, $address]);
            
            // Insert dependants
            
           
    if (!empty($_POST['dependants'])) {
        $stmt = $pdo->prepare("INSERT INTO dependants (user_id, full_name, relationship) VALUES (?, ?, ?)");
        
        foreach ($_POST['dependants'] as $dependent) {
            $cleanName = sanitizeInput($dependent['name']);
            $relationship = sanitizeInput($dependent['relationship']);
            
            // Validate relationship type
            $allowedRelationships = ['spouse', 'child', 'parent', 'sibling', 'other'];
            if (!in_array($relationship, $allowedRelationships)) {
                $relationship = 'other'; // Default value
            }
            
            if (!empty($cleanName)) {
                $stmt->execute([$user_id, $cleanName, $relationship]);
            }
        }
    }
            
            $pdo->commit();
            echo "<p style='color:green;'>Registration successful! Waiting for admin approval.</p>";
        } catch(PDOException $e) {
            $pdo->rollBack();
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registration</title>
    <style>
        .dependants-container {
            margin: 10px 0;
        }
        .dependent-input {
            margin-bottom: 5px;
        }
        .add-dependent {
            background: #4CAF50;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .add-dependent:hover {
            background: #45a049;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>WELCOME TO NINE CLAN NYAKAHURA WELFARE ASSOCIATION.</h1>
    </div>
    <h2>Register</h2>
    <div class="container">
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" placeholder="Enter your username" required>
            
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
            
            <label>Email</label>
            <input type="email" name="email" placeholder="Enter your email" required>
            
            <label>Full Name</label>
            <input type="text" name="full_name" placeholder="Enter your full name" required>
            
            <label>Phone (10-15 digits)</label>
            <input type="text" name="phone" placeholder="Enter your phone number" required>
            
            <label>Address</label>
            <textarea name="address" placeholder="Enter your address" required></textarea>

           <!-- In the form section after address -->
        <label>Dependants</label>
        <div class="dependants-container">
            <div class="dependent-group">
                <div class="dependent-input">
                    <input type="text" name="dependants[0][name]" placeholder="Dependent's full name" required>
                    <select name="dependants[0][relationship]" class="relationship-select">
                        <option value="spouse">Spouse</option>
                        <option value="child">Child</option>
                        <option value="parent">Parent</option>
                        <option value="sibling">Sibling</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        </div>
        <button type="button" class="add-dependent" onclick="addDependent()">Add Another Dependent</button>
            
            <button type="submit">Register</button>
        </form>
        <a href="login.php" class="login-link">Already registered? Log in here</a>
    </div>

    <!-- Footer Section -->
    <div class="footer">
        <div class="social-links">
            <a href="https://www.facebook.com/9CNyakahura" target="_blank">Facebook</a>
            <a href="https://X.com/9CNyahura" target="_blank">X</a>
            <a href="https://www.instagram.com/9CNyakahura" target="_blank">Instagram</a>
        </div>
        <p>Copyright © 9CLAN NYAKAHURA 2025</p>
    </div>

    <!-- Branding Section -->
    <div class="brand">
        <span>Powered by <a href="https://fageratech.cloud" target="_blank">FageraTech</a> Blue Team Technologies</span>
    </div>
    <script>
let dependentCount = 1;
function addDependent() {
    const container = document.querySelector('.dependants-container');
    const newGroup = document.createElement('div');
    newGroup.className = 'dependent-group';
    newGroup.innerHTML = `
        <div class="dependent-input">
            <input type="text" name="dependants[${dependentCount}][name]" placeholder="Dependent's full name">
            <select name="dependants[${dependentCount}][relationship]" class="relationship-select">
                <option value="spouse">Spouse</option>
                <option value="child">Child</option>
                <option value="parent">Parent</option>
                <option value="sibling">Sibling</option>
                <option value="other">Other</option>
            </select>
        </div>
    `;
    container.appendChild(newGroup);
    dependentCount++;
}
</script>

</body>
</html>