<?php
include("auth.php");
include("_helpers.php");

$user_id = $_SESSION['user_id'];

$sql_subs = "SELECT m.*,
    COALESCE((SELECT SUM(se.heures) FROM sessions_etude se WHERE se.matiere_id = m.id AND se.fait = 1), 0) AS done_hours,
    (SELECT COUNT(*) FROM sessions_etude se WHERE se.matiere_id = m.id) AS total_sessions,
    (SELECT COUNT(*) FROM sessions_etude se WHERE se.matiere_id = m.id AND se.fait = 1) AS done_sessions
    FROM matieres m
    WHERE m.utilisateur_id = $user_id
    ORDER BY m.date_examen ASC";

$res_subs = mysqli_query($conn, $sql_subs);
$subjects = [];
while ($row = mysqli_fetch_assoc($res_subs)) {
    $subjects[] = $row;
}

$sql_daily = "SELECT se.date_session, SUM(se.heures) AS heures
              FROM sessions_etude se
              JOIN matieres m ON m.id = se.matiere_id
              WHERE m.utilisateur_id = $user_id
                AND se.fait = 1
                AND se.date_session >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
              GROUP BY se.date_session
              ORDER BY se.date_session ASC";

$res_daily = mysqli_query($conn, $sql_daily);
$dailyMap  = [];
while ($row = mysqli_fetch_assoc($res_daily)) {
    $dailyMap[$row['date_session']] = (float)$row['heures'];
}

$dailyLabels = [];
$dailyData   = [];
for ($i = 29; $i >= 0; $i--) {
    $d             = date('Y-m-d', strtotime("-{$i} days"));
    $dailyLabels[] = date('d/m', strtotime($d));
    $dailyData[]   = $dailyMap[$d] ?? 0;
}

$sql_weekly = "SELECT DATE_FORMAT(se.date_session, '%u-%Y') AS week, SUM(se.heures) AS heures
               FROM sessions_etude se
               JOIN matieres m ON m.id = se.matiere_id
               WHERE m.utilisateur_id = $user_id
                 AND se.fait = 1
                 AND se.date_session >= DATE_SUB(CURDATE(), INTERVAL 56 DAY)
               GROUP BY week
               ORDER BY week ASC";

$res_weekly   = mysqli_query($conn, $sql_weekly);
$weeklyLabels = [];
$weeklyData   = [];
while ($row = mysqli_fetch_assoc($res_weekly)) {
    $weeklyLabels[] = $row['week'];
    $weeklyData[]   = (float)$row['heures'];
}

$sql_tot = "SELECT COUNT(DISTINCT m.id) AS subject_count,
            COALESCE(SUM(m.heures_totales), 0) AS total_planned,
            COALESCE(SUM(m.heures_completees), 0) AS total_done
            FROM matieres m WHERE m.utilisateur_id = $user_id";

$res_tot = mysqli_query($conn, $sql_tot);
$totals  = mysqli_fetch_assoc($res_tot);

$sql_streak = "SELECT DISTINCT se.date_session
               FROM sessions_etude se
               JOIN matieres m ON m.id = se.matiere_id
               WHERE m.utilisateur_id = $user_id AND se.fait = 1
               ORDER BY se.date_session DESC";

$res_streak = mysqli_query($conn, $sql_streak);
$streak     = 0;
$current    = new DateTime('today');

while ($row = mysqli_fetch_assoc($res_streak)) {
    $dt = new DateTime($row['date_session']);
    if ($dt->format('Y-m-d') === $current->format('Y-m-d')) {
        $streak++;
        $current->modify('-1 day');
    } elseif ($dt < $current) {
        break;
    }
}

$globalPct = $totals['total_planned'] > 0 ? round($totals['total_done'] / $totals['total_planned'] * 100) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Statistiques - Smart Study Planner</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
</head>
<body class="app-page">
<?php include '_sidebar.php'; ?>
<main class="main-content">
  <header class="page-header">
    <div class="page-header-left">
      <div class="page-eyebrow">Vue d'ensemble</div>
      <h1 class="page-title">Mes statistiques</h1>
      <p class="page-sub">Visualisez vos progres et celebrez chaque avancee dans votre parcours.</p>
    </div>
  </header>

  <div class="kpi-grid kpi-grid-5">
    <div class="kpi-card">
      <div class="kpi-icon kpi-icon-green"><?= icon('book',22) ?></div>
      <div class="kpi-info"><div class="kpi-value"><?= $totals['subject_count'] ?></div><div class="kpi-label">Matieres</div></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon kpi-icon-blue"><?= icon('clock',22) ?></div>
      <div class="kpi-info"><div class="kpi-value"><?= fmtHoursShort($totals['total_planned']) ?></div><div class="kpi-label">Heures planifiees</div></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon kpi-icon-teal"><?= icon('check-circle',22) ?></div>
      <div class="kpi-info"><div class="kpi-value"><?= fmtHoursShort($totals['total_done']) ?></div><div class="kpi-label">Heures completees</div></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon kpi-icon-orange"><?= icon('award',22) ?></div>
      <div class="kpi-info"><div class="kpi-value"><?= $streak ?></div><div class="kpi-label">Jours consecutifs</div></div>
    </div>
    <div class="kpi-card">
      <div class="kpi-icon kpi-icon-purple"><?= icon('trending-up',22) ?></div>
      <div class="kpi-info"><div class="kpi-value"><?= $globalPct ?>%</div><div class="kpi-label">Progression globale</div></div>
    </div>
  </div>

  <div class="charts-row">
    <div class="chart-card chart-wide">
      <div class="chart-title"><?= icon('calendar',16) ?> Activite des 30 derniers jours</div>
      <div class="chart-sub">Heures d'etude completees par jour</div>
      <div class="chart-wrap"><canvas id="dailyChart"></canvas></div>
    </div>
    <div class="chart-card">
      <div class="chart-title"><?= icon('bar-chart',16) ?> Tendance hebdomadaire</div>
      <div class="chart-sub">Total d'heures par semaine</div>
      <div class="chart-wrap"><canvas id="weeklyChart"></canvas></div>
    </div>
  </div>

  <section class="section">
    <div class="section-header">
      <div>
        <div class="section-title"><?= icon('layers',17) ?> Progression par matiere</div>
        <div class="section-sub">Detail de l'avancement de chaque matiere.</div>
      </div>
    </div>
    <?php if (empty($subjects)): ?>
    <div class="empty-state">
      <div class="empty-icon-wrap"><?= icon('bar-chart',28) ?></div>
      <h3>Aucune donnee disponible</h3>
      <p>Ajoutez des matieres et commencez a etudier pour voir vos statistiques.</p>
      <a href="add_subject.php" class="btn btn-primary"><?= icon('plus',16) ?> Ajouter une matiere</a>
    </div>
    <?php else: ?>
    <div class="subject-stats-list">
      <?php foreach ($subjects as $s):
        $pct      = $s['heures_totales'] > 0 ? round(($s['done_hours'] / $s['heures_totales']) * 100) : 0;
        $days     = max(0, (int)(new DateTime($s['date_examen']))->diff(new DateTime('today'))->days);
        $exp      = strtotime($s['date_examen']) < strtotime('today');
        $doneRate = $s['total_sessions'] > 0 ? round($s['done_sessions'] / $s['total_sessions'] * 100) : 0;
      ?>
      <div class="subj-stat-card">
        <div class="ss-left">
          <div class="ss-name"><?= htmlspecialchars($s['nom']) ?></div>
          <div class="ss-meta">
            <span class="diff-badge diff-<?= diffCss($s['difficulte']) ?>"><?= diffLabel($s['difficulte']) ?></span>
            <span class="ss-exam"><?= icon('calendar',12) ?> <?= date('d/m/Y', strtotime($s['date_examen'])) ?></span>
            <?php if (!$exp): ?>
            <span class="ss-days <?= $days<=7?'urgent':'' ?>"><?= $days ?>j restants</span>
            <?php else: ?><span class="expired-tag">Passe</span><?php endif; ?>
          </div>
        </div>
        <div class="ss-mid">
          <div class="ss-bar-wrap"><div class="ss-bar-fill" style="width:<?= $pct ?>%"></div></div>
          <div class="ss-bar-labels">
            <span><?= fmtHoursShort($s['done_hours']) ?> / <?= fmtHoursShort($s['heures_totales']) ?></span>
            <span class="ss-pct"><?= $pct ?>%</span>
          </div>
        </div>
        <div class="ss-right">
          <div class="ss-circle-wrap">
            <svg class="ss-circle-svg" viewBox="0 0 48 48">
              <circle cx="24" cy="24" r="19" fill="none" stroke="var(--surface-3)" stroke-width="5"/>
              <circle cx="24" cy="24" r="19" fill="none" stroke="var(--primary)" stroke-width="5"
                      stroke-dasharray="<?= round(119.4*$doneRate/100) ?> 119.4"
                      stroke-linecap="round" transform="rotate(-90 24 24)"/>
            </svg>
            <span class="ss-circle-val"><?= $doneRate ?>%</span>
          </div>
          <div class="ss-circle-label">Sessions<br>faites</div>
        </div>
        <div class="ss-actions">
          <a href="plan.php?id=<?= $s['id'] ?>" class="btn-outline btn-sm"><?= icon('eye',14) ?> Planning</a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <div class="chart-card" style="margin-top:20px;max-width:440px">
      <div class="chart-title"><?= icon('target',16) ?> Repartition des heures completees</div>
      <div class="chart-sub">Par matiere</div>
      <div class="chart-wrap" style="height:240px"><canvas id="donutChart"></canvas></div>
    </div>
    <?php endif; ?>
  </section>
</main>

<script>
Chart.defaults.color='#64748B';
Chart.defaults.font.family="'Plus Jakarta Sans',sans-serif";
const PRIMARY='#4A9C7E', ACCENT='#6EC6C0', BORDER_CLR='#E8ECEF';
const palette=[PRIMARY,'#5B8DEF',ACCENT,'#E07A5F','#8B5CF6','#E05A5A','#F59E0B','#06B6D4'];

new Chart(document.getElementById('dailyChart').getContext('2d'),{
    type:'bar',
    data:{
        labels:<?= json_encode($dailyLabels) ?>,
        datasets:[{label:'Heures etudiees',data:<?= json_encode($dailyData) ?>,
        backgroundColor:<?= json_encode(array_map(function($v){ return $v > 0 ? 'rgba(74,156,126,0.7)' : 'rgba(232,240,232,0.8)'; }, $dailyData)) ?>,
        borderColor:PRIMARY,borderWidth:1,borderRadius:4}]
    },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{x:{grid:{color:BORDER_CLR},ticks:{maxTicksLimit:10}},
            y:{grid:{color:BORDER_CLR},beginAtZero:true,ticks:{callback:v=>v+'h'}}}}
});

<?php if (!empty($weeklyLabels)): ?>
new Chart(document.getElementById('weeklyChart').getContext('2d'),{
    type:'line',
    data:{
        labels:<?= json_encode($weeklyLabels) ?>.map((_,i)=>'S'+(i+1)),
        datasets:[{label:'h/semaine',data:<?= json_encode($weeklyData) ?>,borderColor:ACCENT,
        backgroundColor:'rgba(110,198,192,0.1)',fill:true,tension:.4,pointBackgroundColor:ACCENT,pointRadius:4}]
    },
    options:{responsive:true,maintainAspectRatio:false,plugins:{legend:{display:false}},
    scales:{x:{grid:{color:BORDER_CLR}},y:{grid:{color:BORDER_CLR},beginAtZero:true,ticks:{callback:v=>v+'h'}}}}
});
<?php else: ?>
document.getElementById('weeklyChart').closest('.chart-card').insertAdjacentHTML('beforeend','<p style="text-align:center;color:var(--text-muted);padding:30px 0;font-size:.875rem">Pas encore de donnees hebdomadaires</p>');
<?php endif; ?>

<?php if (!empty($subjects)): ?>
new Chart(document.getElementById('donutChart').getContext('2d'),{
    type:'doughnut',
    data:{
        labels:<?= json_encode(array_column($subjects, 'nom')) ?>,
        datasets:[{data:<?= json_encode(array_map(function($s){ return round((float)$s['done_hours'],1); }, $subjects)) ?>,
        backgroundColor:palette.slice(0,<?= count($subjects) ?>),borderWidth:2,borderColor:'#FFFFFF'}]
    },
    options:{responsive:true,maintainAspectRatio:false,cutout:'65%',
    plugins:{legend:{position:'right',labels:{boxWidth:12,padding:14,font:{size:12}}},
    tooltip:{callbacks:{label:ctx=>` ${ctx.parsed}h completees`}}}}
});
<?php endif; ?>
</script>
<script src="script.js"></script>
</body>
</html>
