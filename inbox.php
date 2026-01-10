<?php
SESSION_START();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'config/plugins.php';
?>

<?php include __DIR__ . '/sidebar.php'; ?>

