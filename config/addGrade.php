<?php
session_start();
require_once 'dbcon.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

$username = trim($_POST['username'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$instructor = trim($_POST['instructor'] ?? '');
$prelim = intval($_POST['prelim'] ?? 0);
$midterm = intval($_POST['midterm'] ?? 0);
$finals = intval($_POST['finals'] ?? 0);
 
// Compute average and remarks server-side to ensure consistency
$average = round(($prelim + $midterm + $finals) / 3, 2);
$remarks = $average >= 75 ? 'Passed' : 'Failed';

if (empty($username) || empty($subject) || empty($instructor)) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields.']);
    exit;
}

// Check if grade already exists
$check_sql = "SELECT id FROM grades WHERE username = ? AND subject = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("ss", $username, $subject);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Update existing (also save average and remarks)
    $update_sql = "UPDATE grades SET instructor = ?, prelim = ?, midterm = ?, finals = ?, average = ?, remarks = ? WHERE username = ? AND subject = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("siiidsss", $instructor, $prelim, $midterm, $finals, $average, $remarks, $username, $subject);
    if ($update_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Grade updated successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update grade: ' . $conn->error]);
    }
    $update_stmt->close();
} else {
    // Insert new (also save average and remarks)
    $insert_sql = "INSERT INTO grades (username, subject, instructor, prelim, midterm, finals, average, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssiiids", $username, $subject, $instructor, $prelim, $midterm, $finals, $average, $remarks);
    if ($insert_stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Grade added successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add grade: ' . $conn->error]);
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
?>