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
// ruesteAus() ohne die Anführungszeichen-Escapeung (die ist nur für eval nötig)
echo ruesteAus($dmm);
