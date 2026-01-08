<?php
SESSION_START();
require_once __DIR__ . '/config/dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['username'];
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = 'New passwords do not match.';
        header("Location: profile.php");
        exit();
    }

    // Verify current password
    $sql = "SELECT password FROM enroll WHERE username='$username'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if ($row['password'] === $current_password) {
            // Update password
            $update_sql = "UPDATE enroll SET password='$new_password' WHERE username='$username'";
            if ($conn->query($update_sql) === TRUE) {
                $_SESSION['success'] = 'Password changed successfully.';
            } else {
                $_SESSION['error'] = 'Error updating password.';
            }
        } else {
            $_SESSION['error'] = 'Current password is incorrect.';
        }
    }
    header("Location: profile.php");
    exit();
}
$conn->close();
?>
