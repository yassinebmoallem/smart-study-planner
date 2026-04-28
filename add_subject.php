<?php
include("auth.php");
include("_helpers.php");

$user_id = $_SESSION['user_id'];
$error   = '';
$editId  = isset($_GET['edit']) ? (int)$_GET['edit'] : 0;
$sub     = null;

if ($editId) {
    $sql    = "SELECT * FROM matieres WHERE id = $editId AND utilisateur_id = $user_id";
    $result = mysqli_query($conn, $sql);
    $sub    = mysqli_fetch_assoc($result);
    if (!$sub) { header('Location: dashboard.php'); exit(); }
}

if (isset($_POST['submit'])) {

    $nom        = mysqli_real_escape_string($conn, trim($_POST['name']));
    $difficulte = $_POST['difficulty'];
    $date_exam  = $_POST['exam_date'];
    $h_totales  = (float)$_POST['total_hours'];
    $h_semaine  = (float)$_POST['hours_per_week'];

    if (!$nom || !$difficulte || !$date_exam || !$h_totales || !$h_semaine) {
        $error = 'Veuillez remplir tous les champs.';
    } elseif (strtotime($date_exam) <= strtotime('today')) {
        $error = "La date d'examen doit etre dans le futur.";
    } elseif ($h_totales <= 0 || $h_semaine <= 0) {
        $error = 'Les heures doivent etre superieures a zero.';
    } else {
        if ($editId && $sub) {
            mysqli_query($conn, "UPDATE matieres SET nom = '$nom', difficulte = '$difficulte',
                date_examen = '$date_exam', heures_totales = $h_totales,
                heures_par_semaine = $h_semaine WHERE id = $editId");
            include("planner.php");
            generatePlan($editId);
            header('Location: plan.php?id=' . $editId . '&msg=updated');
            exit();
        } else {
            mysqli_query($conn, "INSERT INTO matieres (utilisateur_id, nom, difficulte, date_examen, heures_totales, heures_par_semaine)
                VALUES ($user_id, '$nom', '$difficulte', '$date_exam', $h_totales, $h_semaine)");
            $newId = mysqli_insert_id($conn);
            include("planner.php");
            generatePlan($newId);
            header('Location: plan.php?id=' . $newId . '&msg=created');
            exit();
        }
    }
}

$minDate = date('Y-m-d', strtotime('+1 day'));

$diffs = [
    'facile'    => ['Facile',    '#4A9C7E', 'check-circle'],
    'moyen'     => ['Moyen',     '#5B8DEF', 'target'],
    'difficile' => ['Difficile', '#E07A5F', 'zap'],
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title><?= $editId?'Modifier':'Ajouter' ?> une matiere - Smart Study Planner</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="app-page">
<?php include '_sidebar.php'; ?>
<main class="main-content">
  <header class="page-header">
    <div class="page-header-left">
      <div class="page-eyebrow">Planning intelligent</div>
      <h1 class="page-title"><?= $editId?'Modifier la matiere':'Nouvelle matiere' ?></h1>
      <p class="page-sub">Renseignez les informations pour generer votre planning personnalise.</p>
    </div>
    <div class="page-header-actions">
      <a href="dashboard.php" class="btn btn-secondary"><?= icon('arrow-left',15) ?> Retour</a>
    </div>
  </header>

  <div class="form-card">
    <?php if ($error): ?>
    <div class="alert alert-error"><?= icon('alert-triangle',16) ?> <?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" id="subjectForm">
      <div class="form-grid">
        <div class="form-group full">
          <label class="form-label">Nom de la matiere <span class="req">*</span></label>
          <input type="text" name="name" class="form-control" placeholder="Ex : Mathematiques, Physique, Histoire..." value="<?= htmlspecialchars($sub['nom'] ?? '') ?>" required>
        </div>

        <div class="form-group full">
          <label class="form-label">Niveau de difficulte <span class="req">*</span></label>
          <div class="difficulty-picker">
            <?php foreach ($diffs as $val => [$label, $color, $ico]): ?>
            <label class="diff-option">
              <input type="radio" name="difficulty" value="<?= $val ?>" <?= ($sub['difficulte'] ?? '') === $val ? 'checked' : '' ?> required>
              <div class="diff-card" style="--dc:<?= $color ?>">
                <span class="diff-icon-wrap" style="color:<?= $color ?>"><?= icon($ico, 20) ?></span>
                <span><?= $label ?></span>
              </div>
            </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Date de l'examen <span class="req">*</span></label>
          <input type="date" name="exam_date" class="form-control" min="<?= $minDate ?>" value="<?= htmlspecialchars($sub['date_examen'] ?? '') ?>" required id="examDate">
          <small class="form-hint" id="daysHint"></small>
        </div>

        <div class="form-group">
          <label class="form-label">Heures totales a etudier <span class="req">*</span></label>
          <div class="input-group">
            <input type="number" name="total_hours" class="form-control" placeholder="Ex : 20" min="1" max="500" step="0.5" value="<?= htmlspecialchars($sub['heures_totales'] ?? '') ?>" required id="totalHours">
            <span class="input-suffix">heures</span>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Heures d'etude par semaine <span class="req">*</span></label>
          <div class="input-group">
            <input type="number" name="hours_per_week" class="form-control" placeholder="Ex : 5" min="1" max="70" step="0.5" value="<?= htmlspecialchars($sub['heures_par_semaine'] ?? '') ?>" required>
            <span class="input-suffix">h/sem</span>
          </div>
        </div>
      </div>

      <div id="planPreview" class="plan-preview" style="display:none">
        <h4><?= icon('cpu',16) ?> Apercu du planning intelligent</h4>
        <div class="preview-stats" id="previewStats"></div>
        <p class="preview-note">Le systeme ajustera automatiquement les sessions selon la difficulte choisie.</p>
      </div>

      <div class="form-footer">
        <a href="dashboard.php" class="btn btn-secondary">Annuler</a>
        <button type="submit" name="submit" class="btn btn-primary">
          <?= icon($editId?'check':'zap',16) ?>
          <?= $editId?'Enregistrer les modifications':'Generer mon planning' ?>
        </button>
      </div>
    </form>
  </div>
</main>
<script>
const examDateInput=document.getElementById('examDate');
const totalHoursInput=document.getElementById('totalHours');
const daysHint=document.getElementById('daysHint');
const planPreview=document.getElementById('planPreview');
const previewStats=document.getElementById('previewStats');
function fmtH(h){const hrs=Math.floor(h);const mins=Math.round((h-hrs)*60);return hrs+'h:'+(mins<10?'0':'')+mins+'min';}
function updatePreview(){
    const ed=new Date(examDateInput.value); const today=new Date(); today.setHours(0,0,0,0);
    const th=parseFloat(totalHoursInput.value);
    if(!examDateInput.value||isNaN(ed)) return;
    const days=Math.ceil((ed-today)/(1000*60*60*24));
    if(days<=0){daysHint.textContent='La date doit etre dans le futur.';planPreview.style.display='none';return;}
    daysHint.textContent=days+' jours restants avant votre examen.';
    if(!isNaN(th)&&th>0){
        const base=th/days; const sess=days;
        planPreview.style.display='block';
        previewStats.innerHTML=`<div class="pstat"><div class="pval">${days}</div><div class="plbl">Jours</div></div><div class="pstat"><div class="pval">${sess}</div><div class="plbl">Sessions</div></div><div class="pstat"><div class="pval">${fmtH(base)}</div><div class="plbl">Base/jour</div></div>`;
    }
}
examDateInput.addEventListener('change',updatePreview);
totalHoursInput.addEventListener('input',updatePreview);
updatePreview();
</script>
<script src="script.js"></script>
</body>
</html>
