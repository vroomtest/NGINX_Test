<?php
// Start the session
session_start();

// Check if password is set in session
if (!isset($_SESSION['password'])) {
    // If not, redirect to home page
    header('Location: index.php');
    exit;
}

// Get the password from session
$password = $_SESSION['password'];

// Logout function
if (isset($_POST['logout'])) {
    // Destroy the session and redirect to home page
    session_destroy();
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Welcome</title>
</head>
<body>
    <h1>Welcome</h1>
    <p>Your password: <?php echo htmlspecialchars($password); ?></p>
    <form method="POST" action="welcome.php">
        <button type="submit" name="logout">Logout</button>
    </form>
</body>
</html>
