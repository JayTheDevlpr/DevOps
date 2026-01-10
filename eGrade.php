<?php
SESSION_START();
include 'config/plugins.php';
require_once __DIR__ . '/config/site.php';
require_once __DIR__ . '/config/dbcon.php';
?>

<nav class="navbar navbar-expand-sm bg-light navbar-light" style="box-shadow: 0 2px 4px rgba(0,0,0,0.1); position: sticky; top: 0; z-index: 1000;">
  <div class="container-fluid container py-4">
    <?php
      $logoPath = __DIR__ . '/' . $SITE_LOGO;
      $logoUrl = htmlspecialchars($SITE_LOGO);
      if (file_exists($logoPath)) { $logoUrl .= '?v=' . filemtime($logoPath); }
    ?>
    <a class="navbar-brand" href="studenthome.php"><img src="<?= $logoUrl ?>" alt="Logo" style="height:40px; width:auto; object-fit:contain;"></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#collapsibleNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="collapsibleNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item">
          <a class="nav-link fs-6" style="width: 4rem;" href="studenthome.php">Home</a>
        </li>
        <li class="nav-item">
          <a class="nav-link fs-6" style="width: 6rem;" href="EGrade.php">E-Grade</a>
        </li>
      </ul>
      <div class="dropdown">
        <button class="btn btn-outline-primary rounded-pill px-4 dropdown-toggle" type="button" id="accountDropdown" data-bs-toggle="dropdown" aria-expanded="false">
         <i class="fas fa-user me-2"></i><?= htmlspecialchars($_SESSION['firstname'] . ' ' . $_SESSION['middlename'] . ' ' . $_SESSION['lastname']) ?>
        </button>
        <ul class="dropdown-menu" aria-labelledby="accountDropdown">
          <li><a class="dropdown-item" href="profile.php">Profile</a></li>
          <li><form method="post" action="index.php" style="display:inline;"><button type="submit" class="dropdown-item">Log-out</button></form></li>
        </ul>
      </div>
    </div>
  </div>
</nav>

<div class="container shadow py-5 text-center mt-2">
<?php
$student_name = '';
$course = $year = $section = $status = '';
if (isset($_SESSION['username'])) {
    $uname = $_SESSION['username'];
    $sql = "SELECT firstname, middlename, lastname, course, year, section, status FROM enroll WHERE username = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param('s', $uname);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($res && $res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $student_name = htmlspecialchars(trim($row['firstname'] . ' ' . $row['middlename'] . ' ' . $row['lastname']));
            $course = htmlspecialchars($row['course'] ?? '');
            $year = htmlspecialchars($row['year'] ?? '');
            $section = htmlspecialchars($row['section'] ?? 'N/A');
            $status = htmlspecialchars($row['status'] ?? '');
        }
        $stmt->close();
    }
} else {
    $student_name = 'Guest';
}
?>
  <div class="row">
    <div class="col-md-1 ms-5 me-3">
        <i class="bi bi-person-workspace" style="font-size: 5rem;"></i>
    </div>
    <div class="col-md-9">
      <div class="row small text-start">
        <div class="col-md-6 col-sm-6 mb-2"><strong>Name:</strong> <span class="text-muted"><?php echo $student_name; ?></span></div>
        <div class="col-md-6 col-sm-6 mb-2"><strong>Course:</strong> <span class="text-muted"><?php echo $course; ?></span></div>
        <div class="col-md-6 col-sm-6 mb-2"><strong>Year:</strong> <span class="text-muted"><?php echo $year; ?></span></div>
        <div class="col-md-6 col-sm-6 mb-2"><strong>Section:</strong> <span class="text-muted"><?php echo $section; ?></span></div>
        <div class="col-md-6 col-sm-6 mb-2"><strong>Status:</strong> <span class="text-muted"><?php echo $status; ?></span></div>
      </div>
    </div>
  </div>
</div>

<?php
// Fetch grades for logged-in user
$grades = [];
if (isset($_SESSION['username'])) {
    $gsql = "SELECT subject, instructor, prelim, midterm, finals, average, remarks FROM grades WHERE username = ?";
    if ($gstmt = $conn->prepare($gsql)) {
        $gstmt->bind_param('s', $uname);
        $gstmt->execute();
        $gres = $gstmt->get_result();
        while ($g = $gres->fetch_assoc()) {
            $grades[] = $g;
        }
        $gstmt->close();
    }
}

function displayGrade($val) {
    if ($val === null || $val === '' || intval($val) === 0) return 'TBA';
    return htmlspecialchars((string)$val);
}
?>

<div class="container card p-4 mt-4">
  <div class="card-header"><h5 class="mb-0">My Grades</h5></div>
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>Subject</th>
            <th>Instructor</th>
            <th>Prelim</th>
            <th>Midterm</th>
            <th>Finals</th>
            <th>Average</th>
            <th>Remarks</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($grades)): ?>
            <tr><td colspan="7" class="text-center">No grades available.</td></tr>
          <?php else: ?>
            <?php foreach ($grades as $g): ?>
              <tr>
                <td><?php echo htmlspecialchars($g['subject']); ?></td>
                <td><?php echo htmlspecialchars($g['instructor']); ?></td>
                <td><?php echo displayGrade($g['prelim']); ?></td>
                <td><?php echo displayGrade($g['midterm']); ?></td>
                <td><?php echo displayGrade($g['finals']); ?></td>
                <td><?php echo displayGrade($g['average']); ?></td>
                  <td>
                    <?php
                      $remark_text = ($g['remarks'] === null || $g['remarks'] === '') ? 'TBA' : $g['remarks'];
                      $r = strtolower(trim((string)$remark_text));
                      $badge = 'secondary';
                      if ($r === 'passed') $badge = 'success';
                      elseif ($r === 'failed') $badge = 'danger';
                      elseif ($r === 'tba') $badge = 'secondary';
                      echo '<span class="badge bg-' . $badge . '">' . htmlspecialchars($remark_text) . '</span>';
                    ?>
                  </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
  