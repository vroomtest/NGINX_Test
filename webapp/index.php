<?php
// Start the session
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password = $_POST['password'];
    if (validatePassword($password)) {
        // If password is valid, store it in session and redirect to welcome page
        $_SESSION['password'] = $password;
        header('Location: welcome.php');
        exit;
    } else {
        // If password is invalid, show an error message
        $error = "Invalid password. Please try again.";
    }
}

// Function to validate password
function validatePassword($password) {
    // Check if the password is at least 10 characters long
    if (strlen($password) < 10) {
        return false;
    }

    // Block common passwords
    $commonPasswords = file('common-passwords.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if (in_array($password, $commonPasswords)) {
        return false;
    }

    return true;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>
    <h1>Login</h1>
    <?php if (isset($error)) echo "<p style='color:red;'>$error</p>"; ?>
    <form method="POST" action="index.php">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
