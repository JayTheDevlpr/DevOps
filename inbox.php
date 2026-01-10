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
  <h1>Inbox</h1>
  <p>All messages from contacts</p>
</div>

<div class="container my-4">
    <?php
    require_once __DIR__ . '/config/dbcon.php';
    
    // Fetch all messages from contact table
    $sql = "SELECT id, name, email, message, date_submitted FROM contact ORDER BY date_submitted DESC";
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <strong><?php echo htmlspecialchars($row['name']); ?></strong>
                    <span class="float-end" style="font-size: 0.9em;">
                        <?php echo date('M d, Y', strtotime($row['date_submitted'])); ?>
                    </span>
                </div>
                <div class="card-body">
                    <p class="card-text"><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></p>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $row['id']; ?>">
                        View Message
                    </button>
                </div>
            </div>

            <!-- Modal for Message Details -->
            <div class="modal fade" id="messageModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="messageModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="messageModalLabel<?php echo $row['id']; ?>">Message from <?php echo htmlspecialchars($row['name']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($row['name']); ?></p>
                            <p><strong>Email:</strong> <a href="mailto:<?php echo htmlspecialchars($row['email']); ?>"><?php echo htmlspecialchars($row['email']); ?></a></p>
                            <p><strong>Date Submitted:</strong> <?php echo date('M d, Y', strtotime($row['date_submitted'])); ?></p>
                            <hr>
                            <p><strong>Message:</strong></p>
                            <p><?php echo htmlspecialchars($row['message']); ?></p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn btn-danger" onclick="confirmDelete(<?php echo $row['id']; ?>)">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php
        }
    } else {
        ?>
        <div class="alert alert-info" role="alert">
            No messages found.
        </div>
        <?php
    }
    
    $conn->close();
    ?>
</div>

<script>
function confirmDelete(messageId) {
    if (confirm('Are you sure you want to delete this message?')) {
        // Create a form and submit it to delete the message
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = 'config/deleteContact.php';
        
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'id';
        input.value = messageId;
        
        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>