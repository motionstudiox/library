<?php
require_once __DIR__ . '/../includes/header.php';

$pageTitle = 'My Profile';
$userId = SessionManager::getCurrentUserId();

// Get user info
$userResult = $db->query("SELECT * FROM users WHERE id = $userId");
$user = $userResult->fetch_assoc();

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = sanitizeInput($_POST['full_name'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $phone = sanitizeInput($_POST['phone'] ?? '');

    // Validate
    if (empty($fullName)) {
        $error = 'Full name is required.';
    } elseif (!isValidEmail($email)) {
        $error = 'Invalid email format.';
    } elseif (!empty($phone) && !isValidPhone($phone)) {
        $error = 'Invalid phone format.';
    } else {
        // Check if email is unique (excluding current user)
        $checkEmail = $db->query("SELECT id FROM users WHERE email = '" . sanitizeSQL($email) . "' AND id != $userId LIMIT 1");
        if ($checkEmail->num_rows > 0) {
            $error = 'Email already in use.';
        } else {
            $stmt = $db->prepare("UPDATE users SET full_name = ?, email = ?, phone = ? WHERE id = ?");
            $stmt->bind_param("sssi", $fullName, $email, $phone, $userId);
            
            if ($stmt->execute()) {
                $message = 'Profile updated successfully!';
                $user['full_name'] = $fullName;
                $user['email'] = $email;
                $user['phone'] = $phone;
            } else {
                $error = 'Failed to update profile.';
            }
        }
    }
}
?>

<h2 class="mb-4"><i class="fas fa-user-circle"></i> My Profile</h2>

<?php if ($message): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-edit"></i> Edit Profile</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small class="text-muted">Username cannot be changed.</small>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($user['email']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="full_name" name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label for="phone" class="form-label">Phone</label>
                        <input type="text" class="form-control" id="phone" name="phone" 
                               value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="555-123-4567">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Account Info</h5>
            </div>
            <div class="card-body">
                <p><strong>Member Since:</strong><br> <?php echo formatDate($user['created_at']); ?></p>
                <p><strong>Account Status:</strong><br> 
                   <?php echo $user['is_active'] ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-danger">Inactive</span>'; ?>
                </p>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
