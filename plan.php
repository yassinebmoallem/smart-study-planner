<?php
include("auth.php");
include("_helpers.php");
include("planner.php");

$user_id = $_SESSION['user_id'];
$id      = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$msg     = $_GET['msg'] ?? '';

$sql    = "SELECT * FROM matieres WHERE id = $id AND utilisateur_id = $user_id";
$result = mysqli_query($conn, $sql);
$sub    = mysqli_fetch_assoc($result);

if (!$sub) { header('Location: dashboard.php'); exit(); }

$sessions = getPlan($id);

$weeks = [];
foreach ($sessions as $s) {
    $wk         = date('W-Y', strtotime($s['date_session']));
    $weeks[$wk][] = $s;
}

$daysLeft     = max(0, (int)(new DateTime($sub['date_examen']))->diff(new DateTime('today'))->days);
$isExpired    = strtotime($sub['date_examen']) < strtotime('today');
$progress     = $sub['heures_totales'] > 0 ? round(($sub['heures_completees'] / $sub['heures_totales']) * 100) : 0;
$doneSessions = 0;
foreach ($sessions as $s) { if ($s['fait']) $doneSessions++; }
$totalSessions = count($sessions);

$dayNames = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= htmlspecialchars($sub['nom']) ?> - Planning</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="app-page">
<?php include '_sidebar.php'; ?>
<main class="main-content">

  <?php if ($msg === 'created'): ?>
  <div class="alert alert-success"><?= icon('check-circle',16) ?> Matiere ajoutee et planning genere avec succes !</div>
  <?php elseif ($msg === 'updated'): ?>
  <div class="alert alert-success"><?= icon('check-circle',16) ?> Planning mis a jour avec succes !</div>
  <?php elseif ($msg === 'regenerated'): ?>
  <div class="alert alert-success"><?= icon('refresh',16) ?> Planning regenere intelligemment selon votre progression.</div>
  <?php endif; ?>

  <header class="page-header">
    <div class="page-header-left">
      <div class="page-eyebrow">Planning detaille</div>
      <h1 class="page-title"><?= htmlspecialchars($sub['nom']) ?></h1>
      <p class="page-sub">
        <span class="diff-badge diff-<?= diffCss($sub['difficulte']) ?>"><?= diffLabel($sub['difficulte']) ?></span>
        &nbsp;Examen le <?= date('d/m/Y', strtotime($sub['date_examen'])) ?>
        <?= !$isExpired ? "&nbsp;· <strong>{$daysLeft} jours</strong> restants" : '&nbsp;· <span class="urgent">Examen passe</span>' ?>
      </p>
    </div>
    <div class="page-header-actions">
      <a href="dashboard.php" class="btn btn-secondary"><?= icon('arrow-left',15) ?> Retour</a>
      <a href="add_subject.php?edit=<?= $sub['id'] ?>" class="btn btn-secondary"><?= icon('edit',15) ?> Modifier</a>
      <?php if (!$isExpired): ?>
      <a href="regenerate.php?id=<?= $sub['id'] ?>" class="btn btn-primary" onclick="return confirm('Regenerer le planning selon votre avancement actuel ?')"><?= icon('refresh',15) ?> Regenerer</a>
      <?php endif; ?>
    </div>
  </header>

  <div class="plan-overview-card">
    <div class="plan-circle-wrap">
      <svg viewBox="0 0 88 88">
        <circle cx="44" cy="44" r="36" fill="none" stroke="var(--surface-3)" stroke-width="8"/>
        <circle cx="44" cy="44" r="36" fill="none" stroke="var(--primary)" stroke-width="8"
                stroke-dasharray="<?= round(226.2*$progress/100) ?> 226.2"
                stroke-linecap="round" transform="rotate(-90 44 44)"/>
      </svg>
      <span class="plan-circle-pct"><?= $progress ?>%</span>
    </div>
    <div class="plan-circle-info">
      <h3>Progression globale</h3>
      <p><?= fmtHoursShort($sub['heures_completees']) ?> completees sur <?= fmtHoursShort($sub['heures_totales']) ?></p>
    </div>
    <div class="plan-stats-row">
      <div class="plan-stat-item"><div class="plan-stat-val"><?= $totalSessions ?></div><div class="plan-stat-lbl">Planifiees</div></div>
      <div class="plan-stat-item"><div class="plan-stat-val"><?= $doneSessions ?></div><div class="plan-stat-lbl">Completees</div></div>
      <div class="plan-stat-item"><div class="plan-stat-val"><?= $totalSessions - $doneSessions ?></div><div class="plan-stat-lbl">Restantes</div></div>
      <div class="plan-stat-item"><div class="plan-stat-val"><?= fmtHoursShort($sub['heures_par_semaine']) ?></div><div class="plan-stat-lbl">Par semaine</div></div>
    </div>
  </div>

  <div class="ai-banner">
    <div class="ai-banner-icon"><?= icon('cpu',20) ?></div>
    <div class="ai-banner-text">
      <strong>Planning ajuste intelligemment</strong> -
      <?php if ($sub['difficulte'] === 'difficile'): ?>Sessions renforcees <strong>+25%</strong> pour maitriser cette matiere exigeante.
      <?php elseif ($sub['difficulte'] === 'facile'): ?>Sessions allegees <strong>-20%</strong> pour un rythme serein sur cette matiere accessible.
      <?php else: ?>Sessions reparties normalement pour une progression equilibree.<?php endif; ?>
    </div>
  </div>

  <?php if (empty($sessions)): ?>
  <div class="empty-state">
    <div class="empty-icon-wrap"><?= icon('calendar',28) ?></div>
    <h3>Aucune session planifiee</h3>
    <p>L'examen est peut-etre passe ou toutes les heures sont completees.</p>
  </div>
  <?php else: ?>
  <section class="section">
    <div class="section-header">
      <div>
        <div class="section-title"><?= icon('list',17) ?> Calendrier des sessions</div>
        <p class="section-hint">Cochez les sessions terminees pour mettre a jour votre progression.</p>
      </div>
    </div>

    <?php foreach ($weeks as $weekKey => $weekSessions):
      $firstDay  = new DateTime($weekSessions[0]['date_session']);
      $weekNum   = $firstDay->format('W');
      $weekStart = (clone $firstDay)->modify('Monday this week')->format('d/m');
      $weekEnd   = (clone $firstDay)->modify('Sunday this week')->format('d/m');
    ?>
    <div class="week-block">
      <div class="week-label-row">
        <span class="week-tag">Semaine <?= $weekNum ?></span>
        <span class="week-date-range"><?= $weekStart ?> - <?= $weekEnd ?></span>
      </div>
      <div class="session-list">
        <?php foreach ($weekSessions as $s):
          $isToday = $s['date_session'] === date('Y-m-d');
          $isPast  = $s['date_session'] < date('Y-m-d') && !$s['fait'];
          $dt      = new DateTime($s['date_session']);
          $dayName = $dayNames[(int)$dt->format('N') - 1];
          $dayFmt  = date('d/m', strtotime($s['date_session']));
        ?>
        <div class="session-row <?= $s['fait']?'done':'' ?> <?= $isToday?'today':'' ?> <?= $isPast?'missed':'' ?>" id="session-<?= $s['id'] ?>">
          <label class="s-check-wrap">
            <input type="checkbox" class="session-cb" <?= $s['fait']?'checked':'' ?> onchange="toggleSession(<?= $s['id'] ?>,this.checked?1:0)">
            <span class="custom-cb"></span>
          </label>
          <div class="s-date-col">
            <div class="s-day"><?= $dayName ?></div>
            <div class="s-date"><?= $dayFmt ?></div>
          </div>
          <div class="s-bar-wrap"><div class="s-bar-fill" style="width:<?= min(100, $s['heures']/6*100) ?>%"></div></div>
          <div class="s-hours"><?= fmtHours($s['heures']) ?></div>
          <?php if ($isToday): ?><span class="tag-today">Aujourd'hui</span><?php endif; ?>
          <?php if ($isPast): ?><span class="tag-missed">Manquee</span><?php endif; ?>
          <?php if ($s['fait']): ?><span class="tag-done"><?= icon('check',11) ?> Fait</span><?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endforeach; ?>
  </section>
  <?php endif; ?>
</main>
<script src="script.js"></script>
</body>
</html>
