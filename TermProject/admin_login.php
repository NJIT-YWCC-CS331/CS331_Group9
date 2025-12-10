<?php
// Start session to track admin login state
session_start();

// Redirect to dashboard if admin is already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin_dashboard.php");
    exit();
}

// Initialize message variables for user feedback
$message = "";
$error = false;

// Hardcoded admin credentials (in production, use database with hashed passwords)
$admin_username = "admin";
$admin_password = "admin123";

// Process login form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get and trim submitted credentials
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Validate credentials against hardcoded values
    if ($username === $admin_username && $password === $admin_password) {
        // Set session variables for successful login
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        
        // Redirect to admin dashboard
        header("Location: admin_dashboard.php");
        exit();
    } else {
        // Set error state and message for invalid credentials
        $error = true;
        $message = "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body { 
            font-family: 'Times New Roman', Times, serif; 
            padding: 20px; 
            text-align: center;
            background-color: #f5f5f5;
        }
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            background-color: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { 
            color: #333;
            margin-bottom: 30px;
        }
        form { 
            width: 100%; 
            text-align: left;
        }
        label { 
            display: block; 
            margin-top: 15px;
            font-weight: bold;
            color: #555;
        }
        input { 
            width: 100%; 
            padding: 10px; 
            margin-top: 5px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
        }
        input:focus {
            outline: none;
            border-color: #007bff;
        }
        .message { 
            padding: 10px;
            margin-bottom: 20px; 
            border-radius: 4px;
            font-weight: bold;
        }
        .error { 
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        button { 
            padding: 12px; 
            width: 100%; 
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: bold;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .back-link {
            margin-top: 20px;
            display: block;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>Admin Login</h2>
        <?php if (!empty($message)): ?>
            <div class="message error">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form method="post" action="">
            <label>Username</label>
            <input type="text" name="username" required autofocus>
            
            <label>Password</label>
            <input type="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>
        <a href="login.php" class="back-link">← Back to User Login</a>
    </div>
</body>
</html>
