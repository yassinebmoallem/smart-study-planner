<?php
include_once("_icons.php");

$currentPage = basename($_SERVER['PHP_SELF']);

$sql_pending = "SELECT COUNT(*) AS today_pending
                FROM sessions_etude se
                JOIN matieres m ON m.id = se.matiere_id
                WHERE m.utilisateur_id = " . $_SESSION['user_id'] . "
                  AND se.date_session = CURDATE()
                  AND se.fait = 0";

$res_pending  = mysqli_query($conn, $sql_pending);
$row_pending  = mysqli_fetch_assoc($res_pending);
$pending      = (int)$row_pending['today_pending'];
$initial      = strtoupper(substr($_SESSION['username'], 0, 1));
?>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<nav class="navbar">
  <button class="hamburger" id="hamburgerBtn" onclick="toggleSidebar()" aria-label="Menu">
    <span></span><span></span><span></span>
  </button>
  <a href="dashboard.php" class="navbar-logo">
    <div class="navbar-logo-box"><?= icon('layers',16) ?></div>
    StudyPlan
  </a>
  <div class="navbar-menu">
    <a href="dashboard.php" class="navbar-link <?= $currentPage==='dashboard.php'?'active':'' ?>"><?= icon('home',15) ?> Accueil</a>
    <a href="add_subject.php" class="navbar-link <?= $currentPage==='add_subject.php'?'active':'' ?>"><?= icon('book',15) ?> Matieres</a>
    <a href="calendar.php"  class="navbar-link <?= $currentPage==='calendar.php' ?'active':'' ?>"><?= icon('calendar',15) ?> Calendrier</a>
    <a href="stats.php"     class="navbar-link <?= $currentPage==='stats.php'    ?'active':'' ?>"><?= icon('bar-chart',15) ?> Statistiques</a>
  </div>
  <div class="navbar-right">
    <?php if ($pending > 0): ?>
    <span style="background:var(--warning-light);color:var(--warning);font-size:.75rem;font-weight:600;padding:4px 12px;border-radius:var(--radius-full);display:inline-flex;align-items:center;gap:5px;"><?= icon('bell',13) ?> <?= $pending ?> session<?= $pending>1?'s':'' ?> aujourd'hui</span>
    <?php endif; ?>
    <span class="navbar-user-name"><?= htmlspecialchars($_SESSION['username']) ?></span>
    <div class="navbar-avatar"><?= $initial ?></div>
    <a href="logout.php" class="btn-outline btn-sm" style="display:inline-flex;align-items:center;gap:6px;"><?= icon('logout',14) ?> Deconnexion</a>
  </div>
</nav>

<aside class="sidebar" id="sidebar">
  <div class="sidebar-section">
    <div class="sidebar-section-label">Navigation</div>
    <a href="dashboard.php" class="nav-item <?= $currentPage==='dashboard.php'?'active':'' ?>">
      <span class="nav-icon"><?= icon('home',16) ?></span> Tableau de bord
      <?php if ($pending > 0): ?><span class="nav-badge"><?= $pending ?></span><?php endif; ?>
    </a>
    <a href="add_subject.php" class="nav-item <?= $currentPage==='add_subject.php'?'active':'' ?>">
      <span class="nav-icon"><?= icon('plus-circle',16) ?></span> Ajouter une matiere
    </a>
    <a href="calendar.php" class="nav-item <?= $currentPage==='calendar.php'?'active':'' ?>">
      <span class="nav-icon"><?= icon('calendar',16) ?></span> Calendrier mensuel
    </a>
    <a href="stats.php" class="nav-item <?= $currentPage==='stats.php'?'active':'' ?>">
      <span class="nav-icon"><?= icon('bar-chart',16) ?></span> Statistiques
    </a>
  </div>

  <?php if ($pending > 0): ?>
  <div class="sidebar-reminder">
    <div class="reminder-icon-wrap"><?= icon('bell',16) ?></div>
    <div class="reminder-text">
      <strong><?= $pending ?> session<?= $pending>1?'s':'' ?> en attente</strong>
      Ne laissez pas passer votre elan aujourd'hui.
    </div>
  </div>
  <?php endif; ?>

  <div class="sidebar-section">
    <div class="sidebar-section-label">Conseils de reussite</div>
  </div>
  <div class="sidebar-secrets">
    <div class="secrets-header">
      <div class="secrets-icon-wrap"><?= icon('lightbulb',14) ?></div>
      Conseils du moment
    </div>
    <div class="secret-tip">Etudiez par blocs de 25 min (Pomodoro) avec 5 min de pause.</div>
    <div class="secret-tip">Revisez le soir ce que vous avez appris le matin pour ancrer la memoire.</div>
    <div class="secret-tip">Commencez par la matiere la plus difficile quand votre esprit est frais.</div>
  </div>

  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-avatar"><?= $initial ?></div>
      <div>
        <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['username']) ?></div>
        <div class="sidebar-user-role">Etudiant(e) motive(e)</div>
      </div>
    </div>
    <a href="logout.php" class="btn-logout"><?= icon('logout',15) ?> Deconnexion</a>
  </div>
</aside>
