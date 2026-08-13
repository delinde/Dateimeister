<?php
/**
 * vorschau.php
 * Wandelt POST'd DmM-Text in HTML um (für die Live-Vorschau im Editor).
 *
 * POST-Parameter: dmm=<DmM-Quelltext>
 */

require_once __DIR__ . '/Verfahren.php';

header('Content-Type: text/html; charset=UTF-8');

$dmm = $_POST['dmm'] ?? '';
$typ = $_POST['typ'] ?? '';

if ($typ === 'dat') {
    // .dat-Dateien: Kommentarzeilen taubenblau, Rest normal
    $out = '';
    foreach (explode("\n", $dmm) as $z) {
        if (ltrim($z) !== '' && ltrim($z)[0] === '#') {
            $out .= '<p style="color:#6c7c98;margin:0;line-height:1.2;font-size:0.9em">'
                  . htmlspecialchars($z) . '</p>';
        } else {
            $out .= ruesteAus($z) . "\n";
        }
    }
    echo $out;
} else {
    // ruesteAus() ohne die Anführungszeichen-Escapeung (die ist nur für eval nötig)
    echo ruesteAus($dmm);
}
