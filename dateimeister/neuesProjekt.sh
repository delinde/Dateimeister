#!/bin/bash
##############################################################
##
##  neuesProjekt.sh
##     Richtet den Dateimeister für ein neues Projekt ein.
##
##  Was dieses Skript tut:
##     1. Fragt Projektname, Tagline und neue Inhaltsdateinamen ab.
##     2. Schreibt  Projektdaten.dat  mit den Eingaben.
##     3. Verschiebt alle Inhaltsdateien aus  Dateien/de/  nach
##        Dateien/de/LoeschMich/  (Gerüstdateien bleiben).
##     4. Bereinigt  Wegweiserdaten.dat :
##        – Kopiert die alte Version nach  LoeschMich/  (als Sicherung).
##        – Löscht alle Einträge, deren .htm-Datei nicht mehr vorhanden ist.
##        – Behält Gerüstdatei-Einträge und den Knoten  Wegweiser .
##        – Fügt die neuen Inhaltsdatei-Namen direkt nach  Wegweiser  ein.
##     5. Legt leere .htm-Vorlagen für die neuen Inhaltsdateinamen an.
##
##  Aufruf (aus dem html-Verzeichnis):
##     bash neuesProjekt.sh
##
##  Voraussetzungen:
##     Geruestdateien.dat   muss im selben Verzeichnis liegen.
##     Python 3             muss verfügbar sein.
##
##############################################################

set -euo pipefail   ## Bei Fehler oder unbelegter Variable abbrechen.

## ── Pfade ────────────────────────────────────────────────────────────────────

SKRIPT_DIR="$(cd "$(dirname "$0")" && pwd)"
DATEIEN_DE="$SKRIPT_DIR/Dateien/de"
GERUESTLISTE="$SKRIPT_DIR/Geruestdateien.dat"
PROJEKTDATEN="$SKRIPT_DIR/Projektdaten.dat"
WEGWEISER="$DATEIEN_DE/Wegweiserdaten.dat"
LOESCHMICH="$DATEIEN_DE/LoeschMich"

## ── Farben ───────────────────────────────────────────────────────────────────

ROT='\033[0;31m'
GRUEN='\033[0;32m'
GELB='\033[1;33m'
HELL='\033[0;37m'
NORMAL='\033[0m'

## ── Hilfsfunktionen ──────────────────────────────────────────────────────────

meldung()  { echo -e "${GELB}$*${NORMAL}"; }
ok()       { echo -e "${GRUEN}  ✓ $*${NORMAL}"; }
info()     { echo -e "${HELL}    $*${NORMAL}"; }
fehler()   { echo -e "${ROT}Fehler: $*${NORMAL}" >&2; exit 1; }

## ── Voraussetzungen prüfen ───────────────────────────────────────────────────

[ -f "$GERUESTLISTE" ] || fehler "Geruestdateien.dat nicht gefunden: $GERUESTLISTE"
[ -d "$DATEIEN_DE"   ] || fehler "Inhaltsdatei-Ordner nicht gefunden: $DATEIEN_DE"
[ -f "$WEGWEISER"    ] || fehler "Wegweiserdaten.dat nicht gefunden: $WEGWEISER"
python3 --version > /dev/null 2>&1 || fehler "python3 nicht gefunden."

## ── Kopfzeile ────────────────────────────────────────────────────────────────

echo ""
meldung "══════════════════════════════════════════════════════"
meldung "  Dateimeister – Einrichtung eines neuen Projekts"
meldung "══════════════════════════════════════════════════════"
echo ""

## ── Benutzereingaben ─────────────────────────────────────────────────────────

echo -e "${HELL}Bitte geben Sie die Projektdaten ein.${NORMAL}"
echo -e "${HELL}(Leere Eingabe übernimmt den Vorschlagswert in [Klammern].)${NORMAL}"
echo ""

##  Vorschlagswerte aus bestehender Projektdaten.dat lesen, falls vorhanden:
VORSCHLAG_NAME=""
VORSCHLAG_NAME2=""
VORSCHLAG_URL=""
if [ -f "$PROJEKTDATEN" ]; then
    VORSCHLAG_NAME=$(grep  "^Projektname="  "$PROJEKTDATEN" | head -1 | cut -d= -f2-)
    VORSCHLAG_NAME2=$(grep "^Projektname2=" "$PROJEKTDATEN" | head -1 | cut -d= -f2-)
    VORSCHLAG_URL=$(grep   "^ProjektURL="   "$PROJEKTDATEN" | head -1 | cut -d= -f2-)
fi

read -rp "  Projektname         [${VORSCHLAG_NAME}]: "  PROJ_NAME
read -rp "  Projektname Zeile 2 [${VORSCHLAG_NAME2}]: " PROJ_NAME2
read -rp "  Projekt-URL         [${VORSCHLAG_URL}]: "   PROJ_URL

##  Vorschlagswerte einsetzen, wenn Eingabe leer:
PROJ_NAME="${PROJ_NAME:-$VORSCHLAG_NAME}"
PROJ_NAME2="${PROJ_NAME2:-$VORSCHLAG_NAME2}"
PROJ_URL="${PROJ_URL:-$VORSCHLAG_URL}"

echo ""
read -rp "  Neue Inhaltsdatei-Namen (Leerzeichen-getrennt, ohne .htm;
  leer lassen wenn keine):
  > " NEUE_DATEIEN

## ── Zusammenfassung und Bestätigung ──────────────────────────────────────────

echo ""
meldung "──────────────────────────────────────────────────────"
meldung "  Zusammenfassung"
meldung "──────────────────────────────────────────────────────"
info "Projektname:    $PROJ_NAME"
info "Zeile 2:        $PROJ_NAME2"
info "URL:            $PROJ_URL"
info "Neue Dateien:   ${NEUE_DATEIEN:-(keine)}"
echo ""
echo -e "${ROT}ACHTUNG: Inhaltsdateien in Dateien/de/ werden nach LoeschMich/ verschoben!${NORMAL}"
echo ""
read -rp "Fortfahren? [j/N]: " BESTAETIGUNG
[[ "$BESTAETIGUNG" == "j" || "$BESTAETIGUNG" == "J" ]] \
    || { echo "Abgebrochen."; exit 0; }
echo ""

## ── 1. Projektdaten.dat schreiben ────────────────────────────────────────────

meldung "── 1. Projektdaten.dat schreiben ──"

cat > "$PROJEKTDATEN" << DATEIENDE
## Projektdaten.dat  –  Projektkonfiguration für den Dateimeister
## ─────────────────────────────────────────────────────────────────
## Erstellt: $(date '+%Y-%m-%d %H:%M')  durch neuesProjekt.sh
## ─────────────────────────────────────────────────────────────────

Projektname=$PROJ_NAME
Projektname2=$PROJ_NAME2
ProjektURL=$PROJ_URL
DATEIENDE

ok "Projektdaten.dat geschrieben."

## ── 2. Inhaltsdateien nach LoeschMich/ verschieben ────────────────────────────

meldung "── 2. Inhaltsdateien verschieben ──"
mkdir -p "$LOESCHMICH"

VERSCHOBEN=0
while IFS= read -r -d '' DATEI; do
    BASENAME="$(basename "$DATEI")"

    ## Prüfe ob Datei in der Gerüstliste steht:
    if grep -qxF "$BASENAME" "$GERUESTLISTE" 2>/dev/null; then
        continue   ## Gerüstdatei – nicht anfassen.
    fi

    mv "$DATEI" "$LOESCHMICH/$BASENAME"
    info "→ $BASENAME"
    VERSCHOBEN=$((VERSCHOBEN + 1))
done < <(find "$DATEIEN_DE" -not -path "$LOESCHMICH/*" -name "*.htm" -type f -print0 | sort -z)

ok "$VERSCHOBEN Inhaltsdatei(en) nach LoeschMich/ verschoben."

## ── 3. Wegweiserdaten.dat bereinigen ─────────────────────────────────────────
##
##  Logik:
##   – Alte Wegweiserdaten.dat wird nach LoeschMich/ kopiert (Sicherung).
##   – Kommentarzeilen (# am Anfang) und Leerzeilen bleiben unverändert.
##   – Jede Datenzeile enthält einen Dateinamen-Stamm als erstes Wort
##     (nach führenden "- " Einrück-Zeichen).
##   – Behalten:  Stamm steht in Geruestdateien.dat  ODER  Stamm.htm existiert.
##   – Gelöscht:  alles andere (keine ## davor, sondern wirklich weg).
##   – Direkt nach der Zeile "Wegweiser" werden die neuen Dateien eingefügt.

meldung "── 3. Wegweiserdaten.dat bereinigen ──"

##  Variablen sicher per Umgebung übergeben (kein Heredoc-Interpolations-Problem):
export DM_WEGWEISER="$WEGWEISER"
export DM_DATEIEN_DE="$DATEIEN_DE"
export DM_GERUESTLISTE="$GERUESTLISTE"
export DM_LOESCHMICH="$LOESCHMICH"
export DM_NEUE_DATEIEN="$NEUE_DATEIEN"

python3 << 'PYENDE'
import re, os, shutil
from datetime import datetime

wegweiser    = os.environ['DM_WEGWEISER']
dateien_de   = os.environ['DM_DATEIEN_DE']
geruestliste = os.environ['DM_GERUESTLISTE']
loeschmich   = os.environ['DM_LOESCHMICH']
neue_dateien = os.environ.get('DM_NEUE_DATEIEN', '')

## Gerüstliste einlesen: Dateinamen-Stämme (mit und ohne Endung) sammeln.
geruestnamen = set()
for zeile in open(geruestliste, encoding="utf-8"):
    z = zeile.strip()
    if not z or z.startswith("#"): continue
    stamm = z.rsplit(".", 1)[0] if "." in z else z   ## Endung abschneiden
    geruestnamen.add(stamm)

## Neue Dateinamen aus der Bash-Eingabe (Leerzeichen-getrennt):
neue_eintraege = [n.strip().rstrip(".htm") for n in neue_dateien.split() if n.strip()]

## Alte Wegweiserdaten.dat nach LoeschMich/ kopieren (Sicherung):
datum = datetime.now().strftime("%Y-%m-%d_%H%M")
sicherung = os.path.join(loeschmich, f"Wegweiserdaten_{datum}.dat")
os.makedirs(loeschmich, exist_ok=True)
shutil.copy2(wegweiser, sicherung)
print(f"    Sicherung: {sicherung}")

## Wegweiserdaten.dat Zeile für Zeile verarbeiten:
zeilen    = open(wegweiser, encoding="utf-8").readlines()
neu       = []
geloescht = 0

for zeile in zeilen:
    bereinigt = zeile.rstrip("\n")

    ## Kommentarzeilen und Leerzeilen unverändert übernehmen:
    if not bereinigt.strip() or bereinigt.lstrip().startswith("#"):
        neu.append(zeile)
        continue

    ## Dateinamen-Stamm extrahieren (erstes Wort nach führenden "- " und Leerzeichen):
    ohne_einrueckung = re.sub(r"^[\s\-]+", "", bereinigt)
    stamm = ohne_einrueckung.split()[0] if ohne_einrueckung.split() else ""

    ## Behalten wenn: Stamm in Gerüstliste  ODER  Stamm.htm noch vorhanden:
    if stamm in geruestnamen or os.path.isfile(os.path.join(dateien_de, stamm + ".htm")):
        neu.append(zeile)

        ## Direkt nach "Wegweiser": neue Inhaltsdateien eintragen:
        if stamm == "Wegweiser" and neue_eintraege:
            for name in neue_eintraege:
                neu.append(f"- {name}\n")
            print(f"    Eingefügt: {len(neue_eintraege)} neue Einträge nach 'Wegweiser'")
            neue_eintraege = []   ## Nur einmal einfügen

    else:
        geloescht += 1   ## Eintrag löschen – nichts anhängen.

open(wegweiser, "w", encoding="utf-8").writelines(neu)
print(f"    Gelöscht:  {geloescht} Einträge")
PYENDE

unset DM_WEGWEISER DM_DATEIEN_DE DM_GERUESTLISTE DM_LOESCHMICH DM_NEUE_DATEIEN
ok "Wegweiserdaten.dat bereinigt."

## ── 4. Neue Inhaltsdateien anlegen ───────────────────────────────────────────

if [ -n "${NEUE_DATEIEN// /}" ]; then
    meldung "── 4. Neue Inhaltsdateien anlegen ──"

    for NAME in $NEUE_DATEIEN; do
        ##  .htm-Endung entfernen, falls der Benutzer sie angegeben hat:
        NAME="${NAME%.htm}"
        DATEI="$DATEIEN_DE/${NAME}.htm"

        if [ -f "$DATEI" ]; then
            info "(bereits vorhanden, übersprungen: ${NAME}.htm)"
        else
            ##  Leere Vorlage anlegen:
            cat > "$DATEI" << VORLAGE
<!-- ${NAME}.htm  –  neu angelegt $(date '+%Y-%m-%d') durch neuesProjekt.sh -->
<ue>${NAME}</ue>
<hr color=#ddb>
<p>Inhalt folgt.</p>
VORLAGE
            info "+ ${NAME}.htm angelegt"
        fi
    done

    ok "Neue Inhaltsdateien angelegt."
fi

## ── Abschluss ─────────────────────────────────────────────────────────────────

echo ""
meldung "══════════════════════════════════════════════════════"
ok "Fertig!"
meldung "══════════════════════════════════════════════════════"
echo ""
echo "  Nächste Schritte:"
info "1. Wegweiserdaten.dat anpassen: neue Einträge einordnen,"
info "   Gerüst-Einträge in die richtige Hierarchie bringen."
info "2. dieSeite.htm: Projektname kommt aus Projektdaten.dat."
info "3. Inhaltsdateien in LoeschMich/ sichten – dann ggf. löschen:"
info "   rm -r $LOESCHMICH"
echo ""
