<?php
session_start();
include("connexion.php");
include("_icons.php");

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error   = '';
$success = '';
$tab     = 'login';

if (isset($_POST['action'])) {

    if ($_POST['action'] === 'register') {
        $tab   = 'register';
        $nom   = $_POST['name'];
        $email = $_POST['email'];
        $pass  = $_POST['password'];
        $pass2 = $_POST['password2'];

        if (!$nom || !$email || !$pass) {
            $error = 'Veuillez remplir tous les champs.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Email invalide.';
        } elseif (strlen($pass) < 6) {
            $error = 'Le mot de passe doit contenir au moins 6 caracteres.';
        } elseif ($pass !== $pass2) {
            $error = 'Les mots de passe ne correspondent pas.';
        } else {
            $email_safe = mysqli_real_escape_string($conn, $email);
            $check      = mysqli_query($conn, "SELECT id FROM utilisateurs WHERE email = '$email_safe'");

            if (mysqli_fetch_assoc($check)) {
                $error = 'Cet email est deja utilise.';
            } else {
                $nom_safe = mysqli_real_escape_string($conn, $nom);
                $hashed   = password_hash($pass, PASSWORD_DEFAULT);
                $sql      = "INSERT INTO utilisateurs (nom, email, mot_de_passe) VALUES ('$nom_safe', '$email_safe', '$hashed')";
                $result   = mysqli_query($conn, $sql);

                if ($result) {
                    $success = 'Compte cree ! Vous pouvez maintenant vous connecter.';
                    $tab     = 'login';
                } else {
                    $error = "Erreur d'inscription.";
                }
            }
        }

    } elseif ($_POST['action'] === 'login') {
        $email = $_POST['email'];
        $pass  = $_POST['password'];

        $email_safe = mysqli_real_escape_string($conn, $email);
        $sql        = "SELECT * FROM utilisateurs WHERE email = '$email_safe'";
        $result     = mysqli_query($conn, $sql);
        $row        = mysqli_fetch_assoc($result);

        if ($row && password_verify($pass, $row['mot_de_passe'])) {
            $_SESSION['user_id']  = $row['id'];
            $_SESSION['username'] = $row['nom'];
            header("Location: dashboard.php");
            exit();
        } else {
            $error = 'Email ou mot de passe incorrect.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Smart Study Planner - Votre espace d'etude</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body class="auth-page">

<nav class="auth-navbar">
  <div class="auth-navbar-logo">
    <div class="auth-navbar-logo-icon"><?= icon('layers',15) ?></div>
    Smart Study Planner
  </div>
  <div style="margin-left:auto;display:flex;gap:10px;align-items:center">
    <span style="font-size:.82rem;color:var(--text-muted)">Deja inscrit ?</span>
    <button class="btn-primary btn-sm" onclick="showTab('login')" style="cursor:pointer">Se connecter</button>
  </div>
</nav>

<div class="auth-container">
  <div class="auth-brand">
    <div class="brand-inner">
      <div class="brand-logo-box"><?= icon('graduation-cap',32) ?></div>
      <h1 class="brand-title">Bienvenue dans votre<br><span class="brand-title-accent">espace d'etude serein</span></h1>
      <p class="brand-desc">Organisez vos revisions avec methode. Un planning intelligent, adapte a votre rythme et a vos objectifs.</p>
      <div class="brand-features">
        <div class="feature-item">
          <div class="feature-icon"><?= icon('target',18) ?></div>
          Plans d'etude personnalises selon votre niveau
        </div>
        <div class="feature-item">
          <div class="feature-icon"><?= icon('cpu',18) ?></div>
          Ajustement intelligent selon la difficulte
        </div>
        <div class="feature-item">
          <div class="feature-icon"><?= icon('calendar',18) ?></div>
          Calendrier et suivi quotidien de vos sessions
        </div>
        <div class="feature-item">
          <div class="feature-icon"><?= icon('bar-chart',18) ?></div>
          Statistiques pour visualiser votre progression
        </div>
      </div>
    </div>
  </div>

  <div class="auth-form-panel">
    <div class="form-box">
      <?php if ($error): ?>
        <div class="alert alert-error"><?= icon('alert-triangle',16) ?> <?= htmlspecialchars($error) ?></div>
      <?php endif; ?>
      <?php if ($success): ?>
        <div class="alert alert-success"><?= icon('check-circle',16) ?> <?= htmlspecialchars($success) ?></div>
      <?php endif; ?>

      <div class="auth-tabs">
        <button class="tab-btn <?= $tab==='login'?'active':'' ?>" onclick="showTab('login')">Se connecter</button>
        <button class="tab-btn <?= $tab==='register'?'active':'' ?>" onclick="showTab('register')">Creer un compte</button>
      </div>

      <div id="tab-login" class="tab-content <?= $tab==='login'?'active':'' ?>">
        <h2 class="form-title">Bon retour parmi nous</h2>
        <p class="form-sub">Reprenez le rythme la ou vous vous etiez arrete.</p>
        <form method="POST">
          <input type="hidden" name="action" value="login">
          <div class="form-group">
            <label class="form-label">Adresse email</label>
            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" placeholder="Votre mot de passe" required>
          </div>
          <button type="submit" class="btn-primary btn-full" style="margin-top:8px">
            <?= icon('chevron-right',16) ?> Se connecter
          </button>
        </form>
        <p class="form-switch">Pas encore de compte ? <a href="#" onclick="showTab('register')">S'inscrire gratuitement</a></p>
      </div>

      <div id="tab-register" class="tab-content <?= $tab==='register'?'active':'' ?>">
        <h2 class="form-title">Creer votre espace d'etude</h2>
        <p class="form-sub">Quelques secondes pour demarrer sereinement.</p>
        <form method="POST">
          <input type="hidden" name="action" value="register">
          <div class="form-group">
            <label class="form-label">Nom complet</label>
            <input type="text" name="name" class="form-control" placeholder="Votre prenom et nom" required>
          </div>
          <div class="form-group">
            <label class="form-label">Adresse email</label>
            <input type="email" name="email" class="form-control" placeholder="votre@email.com" required>
          </div>
          <div class="form-group">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" placeholder="Minimum 6 caracteres" required>
          </div>
          <div class="form-group">
            <label class="form-label">Confirmer le mot de passe</label>
            <input type="password" name="password2" class="form-control" placeholder="Repetez votre mot de passe" required>
          </div>
          <button type="submit" class="btn-primary btn-full" style="margin-top:8px">
            <?= icon('chevron-right',16) ?> Creer mon compte
          </button>
        </form>
        <p class="form-switch">Deja inscrit ? <a href="#" onclick="showTab('login')">Se connecter</a></p>
      </div>
    </div>
  </div>
</div>

<script>
function showTab(name) {
  document.querySelectorAll('.tab-btn').forEach((b,i)=> b.classList.toggle('active',(i===0&&name==='login')||(i===1&&name==='register')));
  document.querySelectorAll('.tab-content').forEach(c=>c.classList.remove('active'));
  document.getElementById('tab-'+name).classList.add('active');
}
</script>
</body>
</html>
