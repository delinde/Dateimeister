<?php
/**
 * holeDateiRoh.php
 * Liefert den rohen DmM-Inhalt einer Inhaltsdatei (ohne ruesteAus-Verarbeitung).
 * Verwendet dieselbe Pfad-Auflösung wie holeDatei() in Verfahren.php.
 *
 * GET-Parameter: datei=de/SachenA/Sache1  (wie $was_holDatei in index.php)
 */

require_once __DIR__ . '/Verfahren.php';

header('Content-Type: text/plain; charset=UTF-8');

$datei = $_GET['datei'] ?? '';
if (!$datei) { http_response_code(400); exit('Kein Pfad angegeben.'); }

// Sicherheit: kein Verzeichnis-Traversal
if (strpos($datei, '..') !== false) { http_response_code(403); exit('Ungültiger Pfad.'); }

// Pfad-Auflösung: identisch mit holeDatei()
$endung      = '.htm';
if (preg_match(',\.(php|htm)$,i', $datei)) $endung = '';
$Dateipfad   = 'Dateien/' . $datei . $endung;

// Unterordner-Suche (case-insensitiv) – wie in holeDatei()
if (!file_exists($Dateipfad) && preg_match(',^([a-z]+)/(.+)$,i', $datei, $m)) {
    $basis          = 'Dateien/' . $m[1];
    $rest           = $m[2];
    $dateiBasename  = basename($rest) . $endung;
    if (strpos($rest, '/') !== false) {
        $praefix  = dirname($rest);
        $gefunden = _sucheInUnterordnernMitPfad($basis, $praefix, $dateiBasename);
        if (!$gefunden) $gefunden = _sucheInUnterordnern($basis, $dateiBasename);
    } else {
        $gefunden = _sucheInUnterordnern($basis, $dateiBasename);
    }
    if ($gefunden) $Dateipfad = $gefunden;
}

// Sicherheitscheck: nur Dateien/de/ darf ausgeliefert werden
$basisReal = realpath(__DIR__ . '/Dateien/de');
$pfadReal  = realpath($Dateipfad);

if (!$pfadReal || !$basisReal || strpos($pfadReal, $basisReal) !== 0) {
    http_response_code(403);
    exit('Zugriff außerhalb von Dateien/de/ nicht erlaubt.');
}

if (!is_file($pfadReal)) {
    http_response_code(404);
    exit('Datei nicht gefunden.');
}

readfile($pfadReal);
