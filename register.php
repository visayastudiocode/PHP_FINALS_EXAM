<?php
session_start();
include "db_connect.php";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE username='$username'");
    
    if (mysqli_num_rows($check) > 0) {
        echo "<p style='color:red;'>Username already taken.</p>";
    } else {
        mysqli_query($conn, 
            "INSERT INTO users(username, password) VALUES('$username', '$password')"
        );

        echo "<p style='color:green;'>Account created successfully!</p>";
        header("Refresh:1; url=login.php");
        exit();
    }
}
?>

<h2>Create Account</h2>
<form method="POST">
    Username: <input type="text" name="username" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit" name="register">Register</button>
</form>

<br>
<a href="login.php">Already have an account? Login</a>
