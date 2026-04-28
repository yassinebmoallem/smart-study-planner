<?php
include("auth.php");
include("_helpers.php");

$user_id = $_SESSION['user_id'];
$year    = isset($_GET['y']) ? (int)$_GET['y'] : (int)date('Y');
$month   = isset($_GET['m']) ? (int)$_GET['m'] : (int)date('m');
$month   = max(1, min(12, $month));

$firstDay  = new DateTime("$year-$month-01");
$lastDay   = new DateTime($firstDay->format('Y-m-t'));
$prevMonth = (clone $firstDay)->modify('-1 month');
$nextMonth = (clone $firstDay)->modify('+1 month');

$sql_sess = "SELECT se.*, m.nom AS subject_name, m.difficulte
             FROM sessions_etude se
             JOIN matieres m ON m.id = se.matiere_id
             WHERE m.utilisateur_id = $user_id
               AND se.date_session >= '" . $firstDay->format('Y-m-d') . "'
               AND se.date_session <= '" . $lastDay->format('Y-m-d') . "'
             ORDER BY se.date_session, m.nom";

$res_sess = mysqli_query($conn, $sql_sess);
$sessions = [];
while ($row = mysqli_fetch_assoc($res_sess)) {
    $sessions[] = $row;
}

$byDate = [];
foreach ($sessions as $s) {
    $byDate[$s['date_session']][] = $s;
}

$sql_exams = "SELECT id, nom, date_examen, difficulte
              FROM matieres
              WHERE utilisateur_id = $user_id
                AND date_examen >= '" . $firstDay->format('Y-m-d') . "'
                AND date_examen <= '" . $lastDay->format('Y-m-d') . "'";

$res_exams   = mysqli_query($conn, $sql_exams);
$examsByDate = [];
while ($row = mysqli_fetch_assoc($res_exams)) {
    $examsByDate[$row['date_examen']][] = $row;
}

$monthNames = ['','Janvier','Fevrier','Mars','Avril','Mai','Juin','Juillet','Aout','Septembre','Octobre','Novembre','Decembre'];
$dayNames   = ['Lun','Mar','Mer','Jeu','Ven','Sam','Dim'];
$startDow   = (int)$firstDay->format('N') - 1;
$calStart   = (clone $firstDay)->modify("-{$startDow} days");
$today      = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Calendrier <?= $monthNames[$month] ?> <?= $year ?> - Smart Study Planner</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="app-page">
<?php include '_sidebar.php'; ?>
<main class="main-content">
  <header class="page-header">
    <div class="page-header-left">
      <div class="page-eyebrow">Vue mensuelle</div>
      <h1 class="page-title">Calendrier de vos revisions</h1>
      <p class="page-sub">Visualisez et gerez toutes vos sessions en un coup d'oeil.</p>
    </div>
    <div class="cal-nav">
      <a href="calendar.php?y=<?= $prevMonth->format('Y') ?>&m=<?= $prevMonth->format('n') ?>" class="btn-outline"><?= icon('arrow-left',15) ?> <?= $monthNames[(int)$prevMonth->format('n')] ?></a>
      <span class="cal-month-label"><?= $monthNames[$month] ?> <?= $year ?></span>
      <a href="calendar.php?y=<?= $nextMonth->format('Y') ?>&m=<?= $nextMonth->format('n') ?>" class="btn-outline"><?= $monthNames[(int)$nextMonth->format('n')] ?> <span style="margin-left:2px"><?= icon('chevron-right',15) ?></span></a>
    </div>
  </header>

  <div class="cal-legend">
    <div class="leg-item"><span class="leg-dot leg-session"></span> Session planifiee</div>
    <div class="leg-item"><span class="leg-dot leg-done"></span> Session realisee</div>
    <div class="leg-item"><span class="leg-dot leg-exam"></span> Jour d'examen</div>
    <div class="leg-item"><span class="leg-dot leg-today"></span> Aujourd'hui</div>
  </div>

  <div class="calendar-grid">
    <?php foreach ($dayNames as $dn): ?>
    <div class="cal-day-header"><?= $dn ?></div>
    <?php endforeach; ?>
    <?php
    $current = clone $calStart;
    $cells   = 0;
    while ($current <= $lastDay || $cells % 7 !== 0):
      $dateStr     = $current->format('Y-m-d');
      $isThisMonth = $current->format('n') == $month;
      $isToday     = $dateStr === $today;
      $daySess     = $byDate[$dateStr] ?? [];
      $dayExams    = $examsByDate[$dateStr] ?? [];
      $isSunday    = $current->format('N') == 7;
      $totalDayH   = 0;
      foreach ($daySess as $ds) $totalDayH += $ds['heures'];
    ?>
    <div class="cal-cell <?= !$isThisMonth?'other-month':'' ?> <?= $isToday?'is-today':'' ?> <?= $isSunday?'is-sunday':'' ?>">
      <div class="cal-cell-head">
        <span class="cal-date-num"><?= $current->format('j') ?></span>
        <?php if ($totalDayH > 0): ?>
        <span class="cal-hours-sum"><?= fmtHoursShort($totalDayH) ?></span>
        <?php endif; ?>
      </div>
      <?php foreach ($dayExams as $ex): ?>
      <div class="cal-exam-badge" title="Examen : <?= htmlspecialchars($ex['nom']) ?>"><?= icon('graduation-cap',10) ?> <?= htmlspecialchars(mb_strimwidth($ex['nom'], 0, 11, '...')) ?></div>
      <?php endforeach; ?>
      <?php foreach ($daySess as $s): ?>
      <div class="cal-session-chip <?= $s['fait']?'chip-done':'' ?> diff-chip-<?= diffCss($s['difficulte']) ?>"
           onclick="toggleSession(<?= $s['id'] ?>,<?= $s['fait']?0:1 ?>)"
           id="cal-session-<?= $s['id'] ?>"
           data-name="<?= htmlspecialchars(mb_strimwidth($s['subject_name'], 0, 10, '...')) ?>"
           title="<?= htmlspecialchars($s['subject_name']) ?> - <?= fmtHours($s['heures']) ?>">
        <?= $s['fait'] ? icon('check',9).' ' : '' ?><?= htmlspecialchars(mb_strimwidth($s['subject_name'], 0, 10, '...')) ?> <span class="chip-h"><?= fmtHoursShort($s['heures']) ?></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php $current->modify('+1 day'); $cells++; endwhile; ?>
  </div>

  <?php
  $totalSess = count($sessions);
  $doneSess  = 0;
  $totalH    = 0;
  $doneH     = 0;
  foreach ($sessions as $s) {
      $totalH += $s['heures'];
      if ($s['fait']) { $doneSess++; $doneH += $s['heures']; }
  }
  $totalExams = 0;
  foreach ($examsByDate as $ex) $totalExams += count($ex);
  ?>
  <div class="month-summary">
    <div class="ms-title"><?= icon('bar-chart',15) ?> Resume de <?= $monthNames[$month] ?> <?= $year ?></div>
    <div class="ms-grid">
      <div class="ms-stat"><div class="ms-val"><?= $totalSess ?></div><div class="ms-lbl">Sessions planifiees</div></div>
      <div class="ms-stat"><div class="ms-val"><?= $doneSess ?></div><div class="ms-lbl">Realisees</div></div>
      <div class="ms-stat"><div class="ms-val"><?= fmtHoursShort($doneH) ?></div><div class="ms-lbl">Heures realisees</div></div>
      <div class="ms-stat"><div class="ms-val"><?= fmtHoursShort($totalH) ?></div><div class="ms-lbl">Heures planifiees</div></div>
      <div class="ms-stat"><div class="ms-val"><?= $totalExams ?></div><div class="ms-lbl">Examens ce mois</div></div>
    </div>
  </div>
</main>
<script>
async function toggleSession(sessionId,done){
  try{
    const res=await fetch('toggle_session.php',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({session_id:sessionId,done})});
    const data=await res.json();
    if(!data.ok)return;
    const chip=document.getElementById('cal-session-'+sessionId);
    if(!chip)return;
    chip.classList.toggle('chip-done',done===1);
    chip.setAttribute('onclick','toggleSession('+sessionId+','+(done===1?0:1)+')');
  }catch(e){console.error(e);}
}
</script>
<script src="script.js"></script>
</body>
</html>
