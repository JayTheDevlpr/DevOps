<?php
SESSION_START();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
include 'config/plugins.php';
require 'config/dbcon.php';

if (!isset($_GET['username'])) {
    header("Location: classroom.php");
    exit();
}

$username = $_GET['username'];
$username_encoded = base64_encode(json_encode($username));
$sql = "SELECT * FROM enroll WHERE username = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Student not found.";
    exit();
}

$student = $result->fetch_assoc();
$name = htmlspecialchars($student['firstname'] . ' ' . $student['middlename'] . ' ' . $student['lastname']);
$course = htmlspecialchars($student['course']);
$year = htmlspecialchars($student['year']);
$section = htmlspecialchars($student['section'] ?? 'N/A');

// Fetch grades
$grades_sql = "SELECT * FROM grades WHERE username = ?";
$grades_stmt = $conn->prepare($grades_sql);
$grades_stmt->bind_param("s", $username);
$grades_stmt->execute();
$grades_result = $grades_stmt->get_result();

// Fetch subjects for the student's course and year
$subjects_sql = "SELECT * FROM subjects WHERE course = ? AND year_level = ?";
$subjects_stmt = $conn->prepare($subjects_sql);
$subjects_stmt->bind_param("ss", $course, $year);
$subjects_stmt->execute();
$subjects_result = $subjects_stmt->get_result();

// Prepare grades by subject
$grades_by_subject = [];
while ($grade = $grades_result->fetch_assoc()) {
    $grade['average'] = round(($grade['prelim'] + $grade['midterm'] + $grade['finals']) / 3, 2);
    $grade['remarks'] = $grade['average'] >= 75 ? 'Passed' : 'Failed';
    $grades_by_subject[$grade['subject']] = $grade;
}
$i = 0;
?>
<?php include __DIR__ . '/sidebar.php'; ?>

<div class="container my-4">
  <h1>View Grades</h1>

  <div class="container card p-4">
    <div class="card-header d-flex justify-content-between align-items-center">
      <h5 class="mb-0">Student Information</h5>
      <a href="classroom.php" class="btn btn-secondary">Back</a>
    </div>
    <div class="card-body mt-2">
            <div class="row">
                <div class="col-md-1">
                    <i class="bi bi-person" style="font-size: 4rem;"></i>
                </div>
                <div class="col-md-9">
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Name:</strong> <?php echo $name; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Username:</strong> <?php echo $username; ?></p>
                    
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <p><strong>Course:</strong> <?php echo $course; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Year:</strong> <?php echo $year; ?></p>
                </div>
                <div class="col-md-3">
                    <p><strong>Section:</strong> <?php echo $section; ?></p>
                </div>
            </div>
            </div>
        </div>
    </div>
  </div>

  <div class="container card p-4 mt-4">
    <div class="card-header">
      <h5 class="mb-0">Subject Grades</h5>
    </div>
    <div class="card-body">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Instructor</th>
            <th>Prelim</th>
            <th>Midterm</th>
            <th>Finals</th>
            <th>Average</th>
            <th>Remarks</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($subject = $subjects_result->fetch_assoc()): 
            $subj_name = $subject['name'];
            $existing = isset($grades_by_subject[$subj_name]) ? $grades_by_subject[$subj_name] : null;
            $i++;
          ?>
            <tr data-subject="<?php echo htmlspecialchars($subj_name); ?>" data-id="<?php echo $i; ?>">
              <td><?php echo htmlspecialchars($subj_name); ?></td>
              <td><?php echo htmlspecialchars($subject['instructor']); ?></td>
              <td><input type="number" step="0.01" class="form-control subj-grade-input" data-type="prelim" value="<?php echo $existing ? htmlspecialchars($existing['prelim']) : ''; ?>"></td>
              <td><input type="number" step="0.01" class="form-control subj-grade-input" data-type="midterm" value="<?php echo $existing ? htmlspecialchars($existing['midterm']) : ''; ?>"></td>
              <td><input type="number" step="0.01" class="form-control subj-grade-input" data-type="finals" value="<?php echo $existing ? htmlspecialchars($existing['finals']) : ''; ?>"></td>
              <td><input type="number" step="0.01" class="form-control" id="subj-average-<?php echo $i; ?>" readonly value="<?php echo $existing ? htmlspecialchars($existing['average']) : ''; ?>"></td>
              <td><input type="text" class="form-control" id="subj-remarks-<?php echo $i; ?>" readonly value="<?php echo $existing ? htmlspecialchars($existing['remarks']) : ''; ?>"></td>
              <td><button type="button" class="btn <?php echo $existing ? 'btn-success' : 'btn-primary'; ?> subj-action-btn" data-action="<?php echo $existing ? 'update' : 'add'; ?>"><?php echo $existing ? 'Update' : 'Add'; ?></button></td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class=" ">
  
  </div>

<script>
var username = JSON.parse(atob('<?php echo $username_encoded; ?>'));
$(document).ready(function(){
    $('.subj-grade-input').on('input', function(){
        var row = $(this).closest('tr');
        var id = row.data('id');
        var prelim = parseInt(row.find('[data-type="prelim"]').val()) || 0;
        var midterm = parseInt(row.find('[data-type="midterm"]').val()) || 0;
        var finals = parseInt(row.find('[data-type="finals"]').val()) || 0;
        var average = (prelim + midterm + finals) / 3;
        $('#subj-average-' + id).val(average.toFixed(2));
        var remarks = average >= 75 ? 'Passed' : 'Failed';
        $('#subj-remarks-' + id).val(remarks);
    });

    $('.subj-action-btn').on('click', function(){
        var row = $(this).closest('tr');
        var subject = row.data('subject');
        var id = row.data('id');
        var instructor = row.find('td:nth-child(2)').text();
        var prelim = row.find('[data-type="prelim"]').val();
        var midterm = row.find('[data-type="midterm"]').val();
        var finals = row.find('[data-type="finals"]').val();
        var average = $('#subj-average-' + id).val();
        var remarks = $('#subj-remarks-' + id).val();

        $.ajax({
            url: 'config/addGrade.php',
            type: 'POST',
          dataType: 'json',
            data: {
                username: username,
                subject: subject,
                instructor: instructor,
                prelim: prelim,
                midterm: midterm,
                finals: finals
            },
          success: function(response){
            var res = response;
            alert(res.message);
            if(res.success){
              location.reload();
            }
          },
          error: function(xhr, status, error){
            var details = xhr.responseText ? '\nResponse: ' + xhr.responseText : '';
            alert('AJAX Error: ' + status + ' - ' + error + details);
            console.error('AJAX error', status, error, xhr.responseText);
          }
        });
    });
});
</script>
  