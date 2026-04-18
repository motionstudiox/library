<?php
define('SKIP_LOGIN_CHECK', true);
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/includes/functions.php';

SessionManager::redirectIfLoggedIn();

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');

    // Validation
    if (empty($username)) $errors[] = 'Username is required.';
    elseif (!isValidUsername($username)) $errors[] = 'Username must be 3-20 characters, alphanumeric and underscore only.';
    
    if (empty($email)) $errors[] = 'Email is required.';
    elseif (!isValidEmail($email)) $errors[] = 'Invalid email format.';
    
    if (empty($password)) $errors[] = 'Password is required.';
    elseif (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters.';
    
    if ($password !== $confirmPassword) $errors[] = 'Passwords do not match.';
    
    if (empty($fullName)) $errors[] = 'Full name is required.';
    
    if (!empty($phone) && !isValidPhone($phone)) $errors[] = 'Invalid phone format.';

    // Check if username exists
    if (empty($errors)) {
        $result = $db->query("SELECT id FROM users WHERE username = '" . sanitizeSQL($username) . "' LIMIT 1");
        if ($result->num_rows > 0) {
            $errors[] = 'Username already exists.';
        }
    }

    // Check if email exists
    if (empty($errors)) {
        $result = $db->query("SELECT id FROM users WHERE email = '" . sanitizeSQL($email) . "' LIMIT 1");
        if ($result->num_rows > 0) {
            $errors[] = 'Email already exists.';
        }
    }

    // Create user
    if (empty($errors)) {
        $passwordHash = hashPassword($password);
        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $email, $passwordHash, $fullName, $phone);
        
        if ($stmt->execute()) {
            redirectWithMessage(SITE_URL . '/login.php', 'Registration successful! Please login.', 'success');
        } else {
            $errors[] = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - <?php echo SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .register-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
            padding: 40px;
            width: 100%;
            max-width: 450px;
        }
        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .register-header h1 {
            font-size: 28px;
            color: #333;
        }
        .form-control {
            border-radius: 5px;
            margin-bottom: 15px;
            padding: 10px 15px;
        }
        .btn-register {
            width: 100%;
            padding: 10px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 5px;
            color: white;
            font-weight: bold;
        }
        .btn-register:hover {
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="register-header">
            <h1><i class="fas fa-book"></i> Register</h1>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i>
                <?php foreach ($errors as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>

            <div class="mb-3">
                <label for="full_name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="full_name" name="full_name" required>
            </div>

            <div class="mb-3">
                <label for="phone" class="form-label">Phone (optional)</label>
                <input type="text" class="form-control" id="phone" name="phone">
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <button type="submit" class="btn btn-register">Register</button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="<?php echo SITE_URL; ?>/login.php">Login here</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
