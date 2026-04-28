<?php
include("auth.php");

header('Content-Type: application/json');

$data       = json_decode(file_get_contents('php://input'), true);
$session_id = (int)($data['session_id'] ?? 0);
$fait       = (int)($data['done'] ?? 0);

if (!$session_id) {
    echo json_encode(['ok' => false]);
    exit();
}

$sql    = "SELECT se.id FROM sessions_etude se JOIN matieres m ON m.id = se.matiere_id WHERE se.id = $session_id AND m.utilisateur_id = " . $_SESSION['user_id'];
$result = mysqli_query($conn, $sql);

if (!mysqli_fetch_assoc($result)) {
    echo json_encode(['ok' => false, 'msg' => 'Non autorise']);
    exit();
}

mysqli_query($conn, "UPDATE sessions_etude SET fait = $fait WHERE id = $session_id");

$res2       = mysqli_query($conn, "SELECT matiere_id FROM sessions_etude WHERE id = $session_id");
$row2       = mysqli_fetch_assoc($res2);
$matiere_id = (int)$row2['matiere_id'];

$res3  = mysqli_query($conn, "SELECT COALESCE(SUM(heures), 0) AS total FROM sessions_etude WHERE matiere_id = $matiere_id AND fait = 1");
$row3  = mysqli_fetch_assoc($res3);
$total = (float)$row3['total'];

mysqli_query($conn, "UPDATE matieres SET heures_completees = $total WHERE id = $matiere_id");

$res4           = mysqli_query($conn, "SELECT heures_totales FROM matieres WHERE id = $matiere_id");
$row4           = mysqli_fetch_assoc($res4);
$heures_totales = (float)$row4['heures_totales'];

$progress = $heures_totales > 0 ? round(($total / $heures_totales) * 100) : 0;

$res5 = mysqli_query($conn, "SELECT COUNT(*) AS done_count FROM sessions_etude WHERE matiere_id = $matiere_id AND fait = 1");
$row5 = mysqli_fetch_assoc($res5);

echo json_encode([
    'ok'              => true,
    'completed_hours' => $total,
    'total_hours'     => $heures_totales,
    'progress'        => $progress,
    'done_count'      => (int)$row5['done_count'],
]);
