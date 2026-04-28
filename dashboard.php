<?php
include("auth.php");
include("_helpers.php");

$user_id = $_SESSION['user_id'];

$sql_subjects = "SELECT m.*,
    (SELECT COUNT(*) FROM sessions_etude se WHERE se.matiere_id = m.id) AS total_sessions,
    (SELECT COUNT(*) FROM sessions_etude se WHERE se.matiere_id = m.id AND se.fait = 1) AS done_sessions
    FROM matieres m
    WHERE m.utilisateur_id = $user_id
    ORDER BY m.date_examen ASC";

$result_subjects = mysqli_query($conn, $sql_subjects);
$subjects        = [];
while ($row = mysqli_fetch_assoc($result_subjects)) {
    $subjects[] = $row;
}

$sql_today = "SELECT se.*, m.nom AS subject_name, m.difficulte
              FROM sessions_etude se
              JOIN matieres m ON m.id = se.matiere_id
              WHERE m.utilisateur_id = $user_id
                AND se.date_session = CURDATE()
              ORDER BY m.date_examen ASC";

$result_today  = mysqli_query($conn, $sql_today);
$todaySessions = [];
while ($row = mysqli_fetch_assoc($result_today)) {
    $todaySessions[] = $row;
}

$todayHours = 0;
foreach ($todaySessions as $s) {
    if (!$s['fait']) $todayHours += $s['heures'];
}

$totalSubjects  = count($subjects);
$activeSubjects = 0;
foreach ($subjects as $s) {
    if (strtotime($s['date_examen']) >= strtotime('today')) $activeSubjects++;
}

$quotes = [
    "La regularite vaut mieux que l'intensite. Avancez doucement, mais avancez chaque jour.",
    "Chaque heure investie aujourd'hui est un pas de plus vers votre reussite de demain.",
    "La serenite dans l'effort est la cle d'un apprentissage durable et profond.",
    "Votre progression, aussi petite soit-elle, merite d'etre celebree.",
];
$quote = $quotes[date('j') % count($quotes)];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Tableau de bord - Smart Study Planner</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="app-page">
<?php include '_sidebar.php'; ?>
<main class="main-content">

  <div class="welcome-banner">
    <div class="wb-icon"><?= icon('sun',28) ?></div>
    <div>
      <div class="wb-title">Bienvenue, <?= htmlspecialchars(explode(' ', $_SESSION['username'])[0]) ?></div>
      <div class="wb-sub">Aujourd'hui, <strong><?= date('l d F Y') ?></strong> - Chaque session compte. Continuez sur votre lancee.</div>
    </div>
    <div class="wb-right">
      <a href="add_subject.php" class="btn-primary"><?= icon('plus',16) ?> Nouvelle matiere</a>
    </div>
  </div>

  <div class="quote-card">
    <div class="quote-text"><?= $quote ?></div>
  </div>

  <div class="stats-grid">
    <div class="stat-card stat-blue">
      <div class="stat-icon-wrap"><?= icon('book',20) ?></div>
      <div class="stat-value"><?= $totalSubjects ?></div>
      <div class="stat-label">Matieres totales</div>
    </div>
    <div class="stat-card stat-green">
      <div class="stat-icon-wrap"><?= icon('target',20) ?></div>
      <div class="stat-value"><?= $activeSubjects ?></div>
      <div class="stat-label">Examens a venir</div>
    </div>
    <div class="stat-card stat-amber">
      <div class="stat-icon-wrap"><?= icon('clock',20) ?></div>
      <div class="stat-value"><?= fmtHoursShort($todayHours) ?></div>
      <div class="stat-label">Restant aujourd'hui</div>
    </div>
    <div class="stat-card stat-purple">
      <div class="stat-icon-wrap"><?= icon('list',20) ?></div>
      <div class="stat-value"><?= count($todaySessions) ?></div>
      <div class="stat-label">Sessions planifiees</div>
    </div>
  </div>

  <?php if ($todaySessions): ?>
  <section class="section">
    <div class="section-title"><?= icon('calendar',17) ?> Vos sessions d'aujourd'hui</div>
    <div class="section-hint">Cochez les sessions realisees pour suivre votre avancement.</div>
    <div class="today-sessions">
      <?php foreach ($todaySessions as $s): ?>
      <div class="today-card <?= $s['fait']?'done':'' ?>" data-session="<?= $s['id'] ?>">
        <div class="tc-left">
          <div class="tc-check" onclick="toggleSession(<?= $s['id'] ?>,<?= $s['fait']?0:1 ?>)">
            <?php if ($s['fait']): ?><?= icon('check',12) ?><?php endif; ?>
          </div>
          <div>
            <div class="tc-name"><?= htmlspecialchars($s['subject_name']) ?></div>
            <div class="tc-meta"><span class="diff-badge diff-<?= diffCss($s['difficulte']) ?>"><?= diffLabel($s['difficulte']) ?></span></div>
          </div>
        </div>
        <div class="tc-hours"><?= fmtHours($s['heures']) ?></div>
      </div>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>

  <section class="section">
    <div class="section-title"><?= icon('book',17) ?> Mes matieres</div>
    <?php if (empty($subjects)): ?>
    <div class="empty-state">
      <div class="empty-icon-wrap"><?= icon('graduation-cap',28) ?></div>
      <h3>Votre espace d'etude est pret</h3>
      <p>Ajoutez votre premiere matiere pour generer un planning personnalise.</p>
      <a href="add_subject.php" class="btn-primary"><?= icon('plus',16) ?> Ajouter ma premiere matiere</a>
    </div>
    <?php else: ?>
    <div class="subjects-grid">
      <?php foreach ($subjects as $sub):
        $daysLeft  = max(0, (int)(new DateTime($sub['date_examen']))->diff(new DateTime('today'))->days);
        $isExpired = strtotime($sub['date_examen']) < strtotime('today');
        $progress  = $sub['heures_totales'] > 0 ? round(($sub['heures_completees'] / $sub['heures_totales']) * 100) : 0;
      ?>
      <div class="subject-card <?= $isExpired?'expired':'' ?>">
        <div class="sc-header">
          <div>
            <div class="sc-name"><?= htmlspecialchars($sub['nom']) ?></div>
            <span class="diff-badge diff-<?= diffCss($sub['difficulte']) ?>"><?= diffLabel($sub['difficulte']) ?></span>
          </div>
          <div class="sc-actions">
            <a href="add_subject.php?edit=<?= $sub['id'] ?>" title="Modifier"><?= icon('edit',13) ?></a>
            <a href="delete_subject.php?id=<?= $sub['id'] ?>" title="Supprimer" onclick="return confirm('Supprimer cette matiere ?')"><?= icon('trash',13) ?></a>
          </div>
        </div>
        <div class="sc-progress-wrap">
          <div class="sc-progress-bar"><div class="sc-progress-fill" style="width:<?= $progress ?>%"></div></div>
          <span class="sc-progress-pct"><?= $progress ?>%</span>
        </div>
        <div class="sc-meta-row">
          <div class="sc-meta-item">
            <span class="meta-label">Examen</span>
            <span class="meta-value"><?= date('d/m/Y', strtotime($sub['date_examen'])) ?></span>
          </div>
          <div class="sc-meta-item">
            <span class="meta-label">Jours restants</span>
            <span class="meta-value <?= $daysLeft<=7?'urgent':'' ?>"><?= $isExpired?'Passe':$daysLeft.'j' ?></span>
          </div>
          <div class="sc-meta-item">
            <span class="meta-label">Progression</span>
            <span class="meta-value"><?= fmtHoursShort($sub['heures_completees']) ?> / <?= fmtHoursShort($sub['heures_totales']) ?></span>
          </div>
        </div>
        <div class="sc-footer">
          <a href="plan.php?id=<?= $sub['id'] ?>" class="btn-outline btn-sm"><?= icon('eye',14) ?> Voir le planning</a>
          <?php if (!$isExpired): ?>
          <a href="regenerate.php?id=<?= $sub['id'] ?>" class="btn-regen" onclick="return confirm('Regenerer le planning ?')"><?= icon('refresh',13) ?> Regenerer</a>
          <?php endif; ?>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </section>
</main>
<script src="script.js"></script>
</body>
</html>
