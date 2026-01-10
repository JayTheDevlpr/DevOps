<?php
SESSION_START();
if (!isset($_SESSION['logged_in'])) {
    header("Location: index.php");
    exit();
}
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

<div class="container shadow py-5 mt-2">
  <h1 class="text-center">Basic Information</h1>
  <p class="text-center lead"> Username: <?= htmlspecialchars($_SESSION['username']) ?> </p>
  <div class="row mx-0 p-3">
    <div class="col-3 navpill-custom-pc text-center">
                <i class="fa fa-user px-1 fa-10x mb-3"></i>
                <br>

                <div class="nav flex-column nav-pills mt-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link active" id="v-pills-1-tab" data-bs-toggle="pill" data-bs-target="#v-pills-1" type="button" role="tab" aria-controls="v-pills-1" aria-selected="true">Personal
                        Information</button>
                    <button class="nav-link" id="v-pills-2-tab" data-bs-toggle="pill" data-bs-target="#v-pills-2" type="button" role="tab" aria-controls="v-pills-2" aria-selected="false">Guardian
                        Information</button>
                    <button class="nav-link" id="v-pills-3-tab" data-bs-toggle="pill" data-bs-target="#v-pills-3" type="button" role="tab" aria-controls="v-pills-3" aria-selected="false">Educational
                        Background</button>
                    <button class="nav-link" id="v-pills-4-tab" data-bs-toggle="pill" data-bs-target="#v-pills-4" type="button" role="tab" aria-controls="v-pills-4" aria-selected="false">Change
                        Password</button>
                </div>

            </div>
    <div class="col-9">
      <?php
        $username = $_SESSION['username'];
        $sql = "SELECT * FROM enroll WHERE username='$username'";
        $result = $conn->query($sql);
        $student = $result->fetch_assoc();
      ?>
      <div class="tab-content" id="v-pills-tabContent">
        <div class="tab-pane fade show active" id="v-pills-1" role="tabpanel" aria-labelledby="v-pills-1-tab">
          <h3 class="mb-3 mt-4">Personal Information</h3>
          <form>
            <div class="row mb-1">
              <div class="col-md-12 col-lg-4 ">
            <div class="mb-1"><label>First Name:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['firstname']) ?>" readonly></div>
            </div>
              <div class="col-md-12 col-lg-4"> 
            <div class="mb-1"><label>Middle Name:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['middlename']) ?>" readonly></div>
            </div>
            <div class="col-md-12 col-lg-4">
            <div class="mb-1"><label>Last Name:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['lastname']) ?>" readonly></div>
            </div>
            </div>
            <div class="row mb-1">
              <div class="col-md-12 col-lg-4">
                <div class="mb-1"><label>Phone Number:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['phonenumber'] ?? '') ?>" readonly></div>
              </div>
              <div class="col-md-12 col-lg-8">
                <div class="mb-1"><label>Email Address:</label><input type="email" class="form-control" value="<?= htmlspecialchars($student['email'] ?? '') ?>" readonly></div>
              </div>
            </div>
            <div class="row mb-1">
              <div class="col-md-12 col-lg-5">
                <div class="mb-1"><label>Sex:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['sex'] ?? '') ?>" readonly></div>
              </div>
              <div class="col-md-12 col-lg-7 ">
                <div class="mb-1"><label>Date of Birth:</label><input type="date" class="form-control" value="<?= htmlspecialchars($student['dob'] ?? '') ?>" readonly></div>
              </div>
            </div>
            <div class="mb-1">
              <label>Home Address:</label><input class="form-control" value="<?= htmlspecialchars($student['address'] ?? '') ?>" readonly></input>
            </div>
            <!-- Add more fields as needed, e.g., address, contact, etc. -->
          </form>
        </div>
        <div class="tab-pane fade" id="v-pills-2" role="tabpanel" aria-labelledby="v-pills-2-tab">
          <h3 class="mb-3 mt-4">Guardian Information</h3>
          <form>
            <div class="mb-3"><label>Guardian Name:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['guardianName'] ?? '') ?>" readonly></div>
            <div class="mb-3"><label>Guardian Contact:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['guardianPhoneNumber'] ?? '') ?>" readonly></div>
            <div class="mb-3"><label>Guardian Address:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['guardianAddress'] ?? '') ?>" readonly></div>
            <!-- Add more fields as needed -->
          </form>
        </div>
        <div class="tab-pane fade" id="v-pills-3" role="tabpanel" aria-labelledby="v-pills-3-tab">
          <h3 class="mb-3 mt-4">Educational Background</h3>
          <form>
            <div class="row mb-1">
              <div class="col-md-12 col-lg-8">
            <div class="mb-3"><label>Elementary School:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['elemName'] ?? '') ?>" readonly></div>
            </div>
              <div class="col-md-12 col-lg-4">
            <div class="mb-3"><label>Year Graduated:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['elemYear'] ?? '') ?>" readonly></div>
            </div>
            </div>
             <div class="row mb-1">
              <div class="col-md-12 col-lg-8">
            <div class="mb-3"><label>Junior High School:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['juniorName'] ?? '') ?>" readonly></div>
            </div>
              <div class="col-md-12 col-lg-4">
            <div class="mb-3"><label>Year Graduated:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['juniorYear'] ?? '') ?>" readonly></div>
            </div>
            </div>
             <div class="row mb-1">
              <div class="col-md-12 col-lg-8">
            <div class="mb-3"><label>Senior High School:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['seniorName'] ?? '') ?>" readonly></div>
            </div>
              <div class="col-md-12 col-lg-4">
            <div class="mb-3"><label>Year Graduated:</label><input type="text" class="form-control" value="<?= htmlspecialchars($student['seniorYear'] ?? '') ?>" readonly></div>
            </div>
            </div>
            <!-- Add more fields as needed -->
          </form>
        </div>
        <div class="tab-pane fade" id="v-pills-4" role="tabpanel" aria-labelledby="v-pills-4-tab">
          <h3 class="mb-3 mt-4">Change Password</h3>
          <?php
            if (isset($_SESSION['success'])) {
              echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
              unset($_SESSION['success']);
            }
            if (isset($_SESSION['error'])) {
              echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['error']) . '</div>';
              unset($_SESSION['error']);
            }
          ?>
          <form method="post" action="change_password.php">
            <div class="mb-3"><label>Current Password:</label><input type="password" class="form-control" placeholder="Enter current password" name="current_password" required></div>
            <div class="mb-3"><label>New Password:</label><input type="password" class="form-control" placeholder="Enter new password" name="new_password" required></div>
            <div class="mb-3"><label>Confirm New Password:</label><input type="password" class="form-control" placeholder="Confirm new password" name="confirm_password" required></div>
            <button type="submit" class="btn btn-primary">Change Password</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>