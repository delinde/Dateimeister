<?php
/**
 * erstelleDatei.php
 * Legt eine neue leere Inhaltsdatei an.
 *
 * POST-Parameter:
 *   pfad     – relativer Pfad wie  Dateien/de/SachenA/Neu.htm
 *   redirect – URL, zu der nach dem Erstellen weitergeleitet wird
 */

require_once __DIR__ . '/Verfahren.php';

$pfad     = $_POST['pfad']     ?? '';
$redirect = $_POST['redirect'] ?? '/';

// Sicherheit: kein Verzeichnis-Traversal, keine fremden Pfade
if (!$pfad || strpos($pfad, '..') !== false) {
    http_response_code(400); exit('Ungültiger Pfad.');
}

$pfadReal  = realpath(__DIR__) . '/' . $pfad;
$basisReal = realpath(__DIR__ . '/Dateien/de');

if (!$basisReal || strpos($pfadReal, $basisReal . '/') !== 0) {
    http_response_code(403); exit('Außerhalb von Dateien/de/ nicht erlaubt.');
}

// Nur .htm und .dat dürfen angelegt werden
if (!preg_match(',\.(htm|dat)$,i', $pfadReal)) {
    http_response_code(400); exit('Nur .htm- und .dat-Dateien können erstellt werden.');
}

// Verzeichnis anlegen falls nötig
$dir = dirname($pfadReal);
if (!is_dir($dir)) {
    if (!@mkdir($dir, 0755, true)) {
        http_response_code(500); exit('Verzeichnis konnte nicht angelegt werden.');
    }
}

// Datei nur anlegen wenn noch nicht vorhanden
if (!file_exists($pfadReal)) {
    $basis  = pathinfo($pfadReal, PATHINFO_FILENAME);
    $datum  = date('Y-m-d');
    if (preg_match(',\.htm$,i', $pfadReal)) {
        $inhalt = "<!-- $basis.htm  –  neu angelegt $datum -->\n"
                . "<ue>$basis</ue>\n"
                . "<hr color=#ddb>\n"
                . "<p>Inhalt folgt.</p>\n";
    } else {
        $inhalt = "## $basis.dat  –  neu angelegt $datum\n";
    }
    if (@file_put_contents($pfadReal, $inhalt) === false) {
        http_response_code(500); exit('Datei konnte nicht geschrieben werden.');
    }
}

// Zurück zur Seite
$redirect = preg_replace(',[\r\n],', '', $redirect);   // Header-Injection verhindern
header('Location: ' . $redirect);
