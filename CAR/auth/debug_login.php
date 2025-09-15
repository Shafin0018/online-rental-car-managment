<?php
require_once __DIR__ . '/config/config.php';

try {
    $config = new AuthConfig();
    $conn = $config->connect();
    echo "<h2>✅ Database Connection: SUCCESS</h2>";
} catch (Exception $e) {
    echo "<h2>❌ Database Connection: FAILED</h2>";
    echo "<p>Error: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h2>👥 Users in Database:</h2>";
$sql = "SELECT id, username, email, role, status FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll();

if (empty($users)) {
    echo "<p>❌ No users found in database!</p>";
    echo "<h3>🔧 Let's create a test user:</h3>";
    
    $testPassword = password_hash('password123', PASSWORD_DEFAULT);
    
    $sql = "INSERT INTO users (username, email, password, role, full_name, email_verified, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([
        'john_test',
        'john@example.com', 
        $testPassword,
        'car_owner',
        'John Test',
        1,
        'active'
    ]);
    
    if ($result) {
        echo "<p>✅ Test user created successfully!</p>";
        echo "<p><strong>Username:</strong> john_test</p>";
        echo "<p><strong>Email:</strong> john@example.com</p>";
        echo "<p><strong>Password:</strong> password123</p>";
    } else {
        echo "<p>❌ Failed to create test user</p>";
    }
} else {
    echo "<table border='1' cellpadding='10' style='border-collapse: collapse;'>";
    echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th></tr>";
    foreach ($users as $user) {
        echo "<tr>";
        echo "<td>{$user['id']}</td>";
        echo "<td>{$user['username']}</td>";
        echo "<td>{$user['email']}</td>";
        echo "<td>{$user['role']}</td>";
        echo "<td>{$user['status']}</td>";
        echo "</tr>";
    }
    echo "</table>";
}

if (isset($_POST['test_login'])) {
    echo "<h2>🔍 Testing Login Credentials:</h2>";
    
    $identifier = $_POST['identifier'];
    $password = $_POST['password'];
    
    echo "<p><strong>Testing with:</strong></p>";
    echo "<p>Identifier: " . htmlspecialchars($identifier) . "</p>";
    echo "<p>Password: " . htmlspecialchars($password) . "</p>";
    
    $sql = "SELECT * FROM users WHERE username = ? OR email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$identifier, $identifier]);
    $user = $stmt->fetch();
    
    if (!$user) {
        echo "<p>❌ User not found with identifier: " . htmlspecialchars($identifier) . "</p>";
    } else {
        echo "<p>✅ User found:</p>";
        echo "<p>ID: {$user['id']}</p>";
        echo "<p>Username: {$user['username']}</p>";
        echo "<p>Email: {$user['email']}</p>";
        echo "<p>Role: {$user['role']}</p>";
        echo "<p>Status: {$user['status']}</p>";
        
        if (password_verify($password, $user['password'])) {
            echo "<p>✅ Password verification: SUCCESS</p>";
        } else {
            echo "<p>❌ Password verification: FAILED</p>";
            echo "<p>Stored hash: " . substr($user['password'], 0, 20) . "...</p>";
            
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            echo "<p>New hash for '{$password}': " . substr($newHash, 0, 20) . "...</p>";
            
            echo "<h3>🔧 Fix the password:</h3>";
            echo "<form method='POST'>";
            echo "<input type='hidden' name='fix_password' value='1'>";
            echo "<input type='hidden' name='user_id' value='{$user['id']}'>";
            echo "<input type='hidden' name='new_password' value='{$password}'>";
            echo "<button type='submit'>Update password hash for this user</button>";
            echo "</form>";
        }
        
        if ($user['status'] !== 'active') {
            echo "<p>⚠️ Account status: {$user['status']} (should be 'active')</p>";
        }
        
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            echo "<p>🔒 Account is locked until: {$user['locked_until']}</p>";
        }
    }
}

if (isset($_POST['fix_password'])) {
    $userId = $_POST['user_id'];
    $newPassword = $_POST['new_password'];
    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    
    $sql = "UPDATE users SET password = ?, login_attempts = 0, locked_until = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([$newHash, $userId]);
    
    if ($result) {
        echo "<p>✅ Password updated successfully! Try logging in now.</p>";
    } else {
        echo "<p>❌ Failed to update password</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login Debug Tool</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { margin: 10px 0; }
        th, td { text-align: left; }
        form { background: #f0f0f0; padding: 15px; margin: 10px 0; border-radius: 5px; }
        input, button { margin: 5px; padding: 8px; }
        button { background: #007cba; color: white; border: none; border-radius: 3px; cursor: pointer; }
    </style>
</head>
<body>
    <h1>🔧 Login Debug Tool</h1>
    
    <h2>🧪 Test Login Credentials:</h2>
    <form method="POST">
        <p>
            <label>Username or Email:</label><br>
            <input type="text" name="identifier" value="john@example.com" required>
        </p>
        <p>
            <label>Password:</label><br>
            <input type="text" name="password" value="password123" required>
        </p>
        <p>
            <button type="submit" name="test_login">Test These Credentials</button>
        </p>
    </form>
    
    <hr>
    <p><strong>Instructions:</strong></p>
    <ol>
        <li>Check if users exist in the database above</li>
        <li>Test login with the form above</li>
        <li>If password verification fails, click the "Update password hash" button</li>
        <li>Then try logging in normally at <a href="login.php">login.php</a></li>
    </ol>
    
    <p><a href="login.php">← Back to Login Page</a></p>
</body>
</html>
