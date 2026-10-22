<?php
/**
 * pruefeStruktur.php
 *
 * Gleicht die Ordner-Struktur in  Dateien/de/  mit  Wegweiserdaten.dat  ab
 * und zeigt Abweichungen in drei Kategorien:
 *
 *  A) Ordner ohne Wegweiser-Eintrag     → wahrscheinlich vergessen einzutragen
 *  B) .htm-Dateien ohne Wegweiser-Eintrag → ebenfalls vermutlich vergessen
 *  C) Wegweiser-Einträge ohne Datei/Ordner → toter Eintrag oder Datei fehlt
 *
 * Aufruf:   php pruefeStruktur.php        (CLI)
 *           https://…/pruefeStruktur.php  (Web – am besten per nginx auf localhost beschränken)
 */

$basisDir     = __DIR__ . "/Dateien/de";
$wegweiserDat = $basisDir . "/Wegweiserdaten.dat";
$geruestDat   = __DIR__   . "/Geruestdateien.dat";

## Ordner-Namen, die beim Scan übersprungen werden (Groß/Klein egal):
$ignoriereOrdner = ['loeschmich', 'bilder', 'saug', 'wd', '__neugutesdeutsch'];

$cli = (php_sapi_name() === 'cli');
if (!$cli) {
    header('Content-Type: text/html; charset=UTF-8');
    echo "<!DOCTYPE html><html><head><meta charset=UTF-8><title>Struktur-Prüfung</title>
<style>
  body { font-family: monospace; padding: 20px; background: #ffffd4; color: #224; }
  h1   { color: #446; }
  h2   { color: #446; border-bottom: 1px solid #aab; }
  .ok  { color: #080; }
  .warn{ color: #a60; }
  .err { color: #c00; }
  b    { color: #224; }
</style></head><body>\n";
}

function ausgabe(string $klasse, string $html, bool $cli): void {
    echo $cli
        ? trim(strip_tags($html)) . "\n"
        : "<p class=$klasse>$html</p>\n";
}
function ueberschrift(string $h, bool $cli, int $ebene = 2): void {
    echo $cli
        ? "\n── $h ──\n"
        : "<h$ebene>$h</h$ebene>\n";
}

## ── Wegweiser-Einträge einlesen ──────────────────────────────────────────────
## Ergebnis: $wwNamen[strtolower(Forumname)] = Forumname
$wwNamen = [];
if (file_exists($wegweiserDat)) {
    foreach (file($wegweiserDat, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $zeile) {
        $zeile = trim($zeile);
        if (!$zeile || str_starts_with($zeile, '#')) continue;
        $zeile = trim(preg_replace(',(//|##).*$,', '', $zeile));
        $zeile = trim(preg_replace(',/\*.*?\*/,',  '', $zeile));
        if (!trim($zeile)) continue;
        ## Forumname = erstes "Wort" aus Buchstaben, Ziffern, Unterstrich.
        ## (Trennzeichen zum ForumnameLang können auch geschützte Leerzeichen sein.)
        $ohneEinrueckung = preg_replace(',^[- ]+,', '', $zeile);
        preg_match(',^[a-zA-Z0-9_]+,', $ohneEinrueckung, $m);
        $wort = $m[0] ?? '';
        if ($wort) $wwNamen[strtolower($wort)] = $wort;
    }
}

## ── Gerüst-Namen einlesen (Stamm ohne Endung) ────────────────────────────────
$geruestNamen = [];
if (file_exists($geruestDat)) {
    foreach (file($geruestDat, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $zeile) {
        $zeile = trim($zeile);
        if (!$zeile || str_starts_with($zeile, '#')) continue;
        $geruestNamen[strtolower(pathinfo($zeile, PATHINFO_FILENAME))] = true;
    }
}

## ── Ordner und .htm-Dateien in Dateien/de/ einmalig einlesen ─────────────────
## $gefundeneOrdner[strtolower(basename)] = [ relPfad1, relPfad2, … ]
## $gefundeneHtm   [strtolower(stamm)   ] = [ relPfad1, … ]
$gefundeneOrdner = [];
$gefundeneHtm    = [];

function inIgnoriertenpfad(string $abs, array $ignLower): bool {
    foreach (explode('/', str_replace('\\', '/', $abs)) as $teil) {
        if (in_array(strtolower($teil), $ignLower, true)) return true;
    }
    return false;
}

if (is_dir($basisDir)) {
    $it = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($basisDir, FilesystemIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    foreach ($it as $eintrag) {
        $abs     = str_replace('\\', '/', $eintrag->getPathname());
        if (inIgnoriertenpfad($abs, $ignoriereOrdner)) continue;
        $name    = $eintrag->getFilename();
        $relPfad = substr($abs, strlen($basisDir) + 1);

        if ($eintrag->isDir()) {
            $gefundeneOrdner[strtolower($name)][] = $relPfad;
        } elseif ($eintrag->isFile()
               && strtolower($eintrag->getExtension()) === 'htm') {
            $stamm = strtolower($eintrag->getBasename('.htm'));
            $gefundeneHtm[$stamm][] = $relPfad;
        }
    }
}

## ── Auswertung ────────────────────────────────────────────────────────────────

ueberschrift("Struktur-Prüfung  –  Dateien/de/ ↔ Wegweiserdaten.dat", $cli, 1);
echo $cli ? "Zeitpunkt: " . date('Y-m-d H:i') . "\n" : "<p>Zeitpunkt: " . date('Y-m-d H:i') . "</p>\n";

## A) Ordner ohne Wegweiser-Eintrag
ueberschrift("A) Ordner ohne Wegweiser-Eintrag", $cli);
$aFunde = 0;
foreach ($gefundeneOrdner as $nameLower => $pfade) {
    if (isset($wwNamen[$nameLower])) continue;
    foreach ($pfade as $p) {
        ausgabe("warn", "⚠  Ordner  <b>$p</b>  – kein Eintrag in Wegweiserdaten.dat", $cli);
        $aFunde++;
    }
}
if (!$aFunde) ausgabe("ok", "✓  Alle Ordner sind in Wegweiserdaten.dat eingetragen.", $cli);

## B) .htm-Dateien ohne Wegweiser-Eintrag
ueberschrift("B) .htm-Dateien ohne Wegweiser-Eintrag", $cli);
$bFunde = 0;
foreach ($gefundeneHtm as $stamm => $pfade) {
    if (isset($wwNamen[$stamm]) || isset($geruestNamen[$stamm])) continue;
    foreach ($pfade as $p) {
        ausgabe("warn", "⚠  Datei   <b>$p</b>  – kein Eintrag in Wegweiserdaten.dat", $cli);
        $bFunde++;
    }
}
if (!$bFunde) ausgabe("ok", "✓  Alle .htm-Dateien sind in Wegweiserdaten.dat eingetragen.", $cli);

## C) Wegweiser-Einträge ohne Datei und ohne Ordner
ueberschrift("C) Wegweiser-Einträge ohne Datei oder Ordner", $cli);
$cFunde = 0;
foreach ($wwNamen as $nameLower => $name) {
    if (isset($geruestNamen[$nameLower])) continue;         ## Gerüst-Einträge sind OK
    $hatDatei  = isset($gefundeneHtm[$nameLower]);
    $hatOrdner = isset($gefundeneOrdner[$nameLower]);
    if (!$hatDatei && !$hatOrdner) {
        ausgabe("err", "✗  Wegweiser-Eintrag  <b>$name</b>  – weder .htm-Datei noch Ordner gefunden", $cli);
        $cFunde++;
    }
}
if (!$cFunde) ausgabe("ok", "✓  Alle Wegweiser-Einträge haben eine Datei oder einen Ordner.", $cli);

if (!$cli) echo "</body></html>\n";
