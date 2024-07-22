<!DOCTYPE html>
<html>
<head><title>Login</title></head>
<body>
  <form method="post">
    Password: <input type="password" name="password" required>
    <button type="submit">Login</button>
  </form>
  <?php
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $password = $_POST['password'];
        $common_passwords = file('common-passwords.txt', FILE_IGNORE_NEW_LINES);

        if (in_array($password, $common_passwords)) {
            echo '<p style="color:red">Password is too common.</p>';
        } else {
            header('Location: welcome.php');
            exit();
        }
    }
  ?>
</body>
</html>
