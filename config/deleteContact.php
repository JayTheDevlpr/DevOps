<?php
SESSION_START();
require_once 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    
    $sql = "DELETE FROM contact WHERE id = $id";
    
    if ($conn->query($sql) === TRUE) {
        $_SESSION['success'] = "Message deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting message: " . $conn->error;
    }
    
    $conn->close();
    header("Location: ../inbox.php");
    exit();
} else {
    header("Location: ../inbox.php");
    exit();
}
?>
