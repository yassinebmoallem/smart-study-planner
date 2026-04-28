<?php
include_once("connexion.php");

function generatePlan($matiere_id) {
    global $conn;

    $sql    = "SELECT * FROM matieres WHERE id = $matiere_id";
    $result = mysqli_query($conn, $sql);
    $sub    = mysqli_fetch_assoc($result);

    if (!$sub) return;

    $today    = new DateTime('today');
    $examDate = new DateTime($sub['date_examen']);
    $daysLeft = (int) $today->diff($examDate)->days;

    if ($daysLeft <= 0) return;

    $hoursLeft = max(0, $sub['heures_totales'] - $sub['heures_completees']);

    $baseDailyHours = $hoursLeft / $daysLeft;

    if ($sub['difficulte'] === 'difficile') {
        $multiplier = 1.25;
    } elseif ($sub['difficulte'] === 'facile') {
        $multiplier = 0.80;
    } else {
        $multiplier = 1.00;
    }

    $dailyHours = min(6, max(0.5, round($baseDailyHours * $multiplier, 2)));

    mysqli_query($conn, "DELETE FROM sessions_etude WHERE matiere_id = $matiere_id AND fait = 0");

    $current        = clone $today;
    $remainingHours = $hoursLeft;

    while ($remainingHours > 0 && $current < $examDate) {
        $sessionHours = round(min($dailyHours, $remainingHours), 2);
        $dateStr      = $current->format('Y-m-d');

        mysqli_query($conn, "INSERT INTO sessions_etude (matiere_id, date_session, heures)
                             VALUES ($matiere_id, '$dateStr', $sessionHours)");

        $remainingHours -= $sessionHours;
        $current->modify('+1 day');
    }
}

function getPlan($matiere_id) {
    global $conn;

    $sql    = "SELECT * FROM sessions_etude WHERE matiere_id = $matiere_id ORDER BY date_session ASC";
    $result = mysqli_query($conn, $sql);

    $sessions = [];
    while ($row = mysqli_fetch_assoc($result)) {
        $sessions[] = $row;
    }

    return $sessions;
}
