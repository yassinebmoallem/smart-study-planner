<?php
include("auth.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql    = "SELECT id FROM matieres WHERE id = $id AND utilisateur_id = " . $_SESSION['user_id'];
$result = mysqli_query($conn, $sql);

if (mysqli_fetch_assoc($result)) {
    mysqli_query($conn, "DELETE FROM matieres WHERE id = $id");
}

header("Location: dashboard.php");
exit();
