<?php
SESSION_START();
require_once 'dbcon.php';

if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: ../index.php");
    exit();
}

$username = $_POST['username'];
$password = $_POST['password'];

$redirect = null;

if (strpos($username, '@admin') !== false) {
    // Check accounts table for admin
    $sql = "SELECT * FROM accounts WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'admin';
        $redirect = "../content_management.php";
    }
} elseif (strpos($username, '@student') !== false) {
    // Check enroll table for student
    $sql = "SELECT firstname, middlename, lastname FROM enroll WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $_SESSION['logged_in'] = true;
        $_SESSION['user_type'] = 'student';

        $_SESSION['username'] = $username;
        $_SESSION['firstname'] = $row['firstname'];
        $_SESSION['middlename'] = $row['middlename'];
        $_SESSION['lastname'] = $row['lastname'];
        $redirect = "../studenthome.php";
    }
}

if ($redirect) {
    header("Location: $redirect");
    exit();
} else {
    $_SESSION['error'] = 'Invalid username or password';
    header("Location: ../index.php");
    exit();
}

$conn->close();
?>