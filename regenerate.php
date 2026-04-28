<?php
include("auth.php");
include("planner.php");

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$sql    = "SELECT id FROM matieres WHERE id = $id AND utilisateur_id = " . $_SESSION['user_id'];
$result = mysqli_query($conn, $sql);

if (mysqli_fetch_assoc($result)) {
    generatePlan($id);
}

header("Location: plan.php?id=" . $id . "&msg=regenerated");
exit();
