<?php
/**
 * speichereDatei.php
 * Speichert DmM-Inhalt in eine Inhaltsdatei.
 * Legt vorher eine Sicherungskopie in  .versionen/  an.
 *
 * POST-Parameter:
 *   dmm  – neuer DmM-Quelltext
 *   pfad – Dateipfad wie $was_holDatei, z.B. "de/SachenA/Sache1"
 */

require_once __DIR__ . '/Verfahren.php';

header('Content-Type: text/plain; charset=UTF-8');

$dmm  = $_POST['dmm']  ?? null;
$pfad = $_POST['pfad'] ?? '';

if ($dmm === null) { http_response_code(400); exit('Kein Inhalt (dmm) übermittelt.'); }
if (!$pfad)        { http_response_code(400); exit('Kein Pfad (pfad) übermittelt.');  }

// Sicherheit: kein Verzeichnis-Traversal
if (strpos($pfad, '..') !== false) { http_response_code(403); exit('Ungültiger Pfad.'); }

// Dieselbe Pfad-Auflösung wie holeDatei() / holeDateiRoh.php
$endung     = '.htm';
if (preg_match(',\.(php|htm)$,i', $pfad)) $endung = '';
$Dateipfad  = __DIR__ . '/Dateien/' . $pfad . $endung;

if (!file_exists($Dateipfad) && preg_match(',^([a-z]+)/(.+)$,i', $pfad, $m)) {
    $basis         = __DIR__ . '/Dateien/' . $m[1];
    $rest          = $m[2];
    $dateiBasename = basename($rest) . $endung;
    if (strpos($rest, '/') !== false) {
        $praefix  = dirname($rest);
        $gefunden = _sucheInUnterordnernMitPfad($basis, $praefix, $dateiBasename);
        if (!$gefunden) $gefunden = _sucheInUnterordnern($basis, $dateiBasename);
    } else {
        $gefunden = _sucheInUnterordnern($basis, $dateiBasename);
    }
    if ($gefunden) $Dateipfad = $gefunden;
}

// Sicherheitscheck: nur Dateien/de/ darf beschrieben werden
$basisReal = realpath(__DIR__ . '/Dateien/de');
$pfadReal  = realpath($Dateipfad);

if (!$pfadReal || !$basisReal || strpos($pfadReal, $basisReal) !== 0) {
    http_response_code(403);
    exit('Schreiben außerhalb von Dateien/de/ nicht erlaubt.');
}
if (!is_file($pfadReal)) {
    http_response_code(404);
    exit('Zieldatei nicht gefunden: ' . basename($pfadReal));
}

// Sicherungskopie in  .versionen/  anlegen
$versDir = dirname($pfadReal) . '/.versionen';
if (!is_dir($versDir)) @mkdir($versDir, 0755, true);
$stempel  = date('Y-m-d_Hi');
$sicherung = $versDir . '/' . basename($pfadReal) . '_' . $stempel;
if (!file_exists($sicherung)) {   // nicht zweimal pro Minute sichern
    @copy($pfadReal, $sicherung);
}

// Datei schreiben
if (file_put_contents($pfadReal, $dmm) !== false) {
    echo 'OK';
} else {
    http_response_code(500);
    exit('Schreibfehler – Dateisystemrechte prüfen.');
}
