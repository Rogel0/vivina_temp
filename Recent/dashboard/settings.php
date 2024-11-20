<?php
session_start();
include('../connection.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    header("Location: ../index.php");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['id']; 
$query = "SELECT first_name, middle_name, last_name, Email, img FROM users WHERE id = ?";
$stmt = $con->prepare($query);
$stmt->bindParam(1, $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Handle form submission
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $avatar = $_FILES['avatar'];

    // Update user info
    $update_query = "UPDATE users SET first_name = ?, middle_name = ?, last_name = ?, Email = ? WHERE id = ?";
    $update_stmt = $con->prepare($update_query);
    $update_stmt->bindParam(1, $first_name);
    $update_stmt->bindParam(2, $middle_name);
    $update_stmt->bindParam(3, $last_name);
    $update_stmt->bindParam(4, $email);
    $update_stmt->bindParam(5, $user_id, PDO::PARAM_INT);
    $update_stmt->execute();

    // Handle password reset
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $password_query = "UPDATE users SET Password = ? WHERE id = ?";
        $password_stmt = $con->prepare($password_query);
        $password_stmt->bindParam(1, $hashed_password);
        $password_stmt->bindParam(2, $user_id, PDO::PARAM_INT);
        $password_stmt->execute();
    }

    // Handle avatar upload
    if ($avatar['error'] == 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($avatar["name"]);
        move_uploaded_file($avatar["tmp_name"], $target_file);

        $avatar_query = "UPDATE users SET img = ? WHERE id = ?";
        $avatar_stmt = $con->prepare($avatar_query);
        $avatar_stmt->bindParam(1, $target_file);
        $avatar_stmt->bindParam(2, $user_id, PDO::PARAM_INT);
        $avatar_stmt->execute();
    }

    // Redirect to the same page to show updated info
    header("Location: settings.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="../assets/css/style.css"> 
</head>
<body>
    <div class="container">
        <h1>Settings</h1>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="first_name">First Name:</label>
                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="middle_name">Middle Name:</label>
                <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($user['middle_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="last_name">Last Name:</label>
                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['Email']); ?>" required>
            </div>
            <div class="form-group">
                <label for="password">Reset Password:</label>
                <input type="password" id="password" name="password" placeholder="Leave blank to keep current password">
            </div>
            <div class="form-group">
                <label for="avatar">Profile Avatar:</label>
                <input type="file" id="avatar" name="avatar" accept="image/*">
            </div>
            <div class="form-group">
                <button type="submit">Save Changes</button>
            </div>
        </form>
        <div class="current-avatar">
            <h2>Current Avatar:</h2>
            <img src="<?php echo htmlspecialchars($user['img']); ?>" alt="Current Avatar" height="100" width="100">
        </div>
    </div>
</body>
</html>