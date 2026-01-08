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
  <h1>Welcome to <?= htmlspecialchars($SITE_INST) ?></h1>
  <p class="lead"> Digital Enrollment System</p>
</div>