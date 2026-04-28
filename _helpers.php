<?php
function fmtHours($h) {
    $h     = max(0, $h);
    $hours = (int) floor($h);
    $mins  = (int) round(($h - $hours) * 60);
    if ($mins >= 60) { $hours++; $mins = 0; }
    return $hours . 'h:' . str_pad($mins, 2, '0', STR_PAD_LEFT) . 'min';
}

function fmtHoursShort($h) {
    $h     = max(0, $h);
    $hours = (int) floor($h);
    $mins  = (int) round(($h - $hours) * 60);
    if ($mins >= 60) { $hours++; $mins = 0; }
    return $mins === 0 ? $hours . 'h' : $hours . 'h:' . str_pad($mins, 2, '0', STR_PAD_LEFT) . 'min';
}

function diffCss($difficulte) {
    $map = ['facile' => 'easy', 'moyen' => 'medium', 'difficile' => 'hard'];
    return $map[$difficulte] ?? 'medium';
}

function diffLabel($difficulte) {
    $map = ['facile' => 'Facile', 'moyen' => 'Moyen', 'difficile' => 'Difficile'];
    return $map[$difficulte] ?? $difficulte;
}
