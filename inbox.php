<?php
SESSION_START();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'config/plugins.php';
?>

<?php include __DIR__ . '/sidebar.php'; ?>

<div class="container my-4">
  <h1>Classroom</h1>
  <p>This is the inbox (formerly dashboard). Replace with your classroom/dashboard content.</p>
</div>
