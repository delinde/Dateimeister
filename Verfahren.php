<?php


$templatefolder = "Dateien";


##############################################################
##  leseProjektdaten()
##     Liest  Projektdaten.dat  (Schlüssel=Wert-Format) ein
##     und gibt ein assoziatives Array zurück.
##     Kommentarzeilen (##) und Leerzeilen werden übersprungen.
##############################################################
function leseProjektdaten($datei = "Projektdaten.dat") {
    $daten = [];
    if (!file_exists($datei)) return $daten;
    foreach (file($datei, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $zeile) {
        if (str_starts_with(ltrim($zeile), '#')) continue;
        $pos = strpos($zeile, '=');
        if ($pos === false) continue;
        $schluessel = trim(substr($zeile, 0, $pos));
        $wert       = trim(substr($zeile, $pos + 1));
        $daten[$schluessel] = $wert;
    }
    return $daten;
}



function ruesteAus($dingen)  {
  $dingen = preg_replace(",<ue[^0]*?>,i"  ,  "<span class='Ueberschrift'>" , $dingen);    // ue, Ueberschrift
  $dingen = preg_replace(",<ue[^>]*?0>,i" ,  "<span class='Ueberschrift0'>", $dingen);   // ue0, Ueberschrift0
  $dingen = preg_replace(",</ue.*?>,i" , "</span>", $dingen);     // ue0, ueberschrift0
  $dingen = preg_replace(",<anm*>," ,  "<p class='Anmerkung'>", $dingen);
  $dingen = preg_replace(",<rot>,",    "<span class=rot>",   $dingen);
  $dingen = preg_replace(",<gruen>,",  "<span class=gruen>", $dingen);
  $dingen = preg_replace(",</rot>,",   "</span>", $dingen);
  $dingen = preg_replace(",</gruen>,", "</span>", $dingen);
  $dingen = preg_replace(",</anm*>,",  "</p>", $dingen);
  $dingen = preg_replace(",[/#]\'\'\',",    "</b>", $dingen);
  $dingen = preg_replace(",\<\<,",  "&#10216;", $dingen);           // Graphem-Klammern
  $dingen = preg_replace(",\>\>,",  "&#10217;", $dingen);           // Graphem-Klammern
  $dingen = preg_replace(",\'\'\',",     "<b>",  $dingen);
  $dingen = preg_replace(",[/#]\'\',",    "</i>", $dingen);
  $dingen = preg_replace(",\'\',",     "<i>",  $dingen);
  $dingen = preg_replace(",[/#]\"\",",  "&#147;", $dingen);
  $dingen = preg_replace(",\"\",",      "&#132;", $dingen);
  $dingen = preg_replace(",</anm*>,",   "</p>", $dingen);
  $dingen = preg_replace(",</anm*>,",   "</p>", $dingen);                
                          ####                Vorausschau /          lookahead:  (?=Suchzeichen)
                          ####    verneinende Vorausschau / negative lookahead:  (?!Suchzeichen)
                          ####    verneinende   Rückschau / negative lookbehind: (?<!Suchzeichen)
                          ####                  Rückschau /          lookbehind: (?<=Suchzeichen)
#.$dingen = preg_replace(",(?<!!)--(?=[ \d\.\,\!\?]),",  "&#150;", $dingen);       //   -- gefolgt von Leerzeichen oder Ziffer oder .,!?
  $dingen = preg_replace(",(?<!!)--(?![>]),"          ,  "&#150;", $dingen);       //  HTML-Kommentare heillassen, kein  !--  oder  -->  ändern 
##$dingen = preg_replace(",\n(\s*\n)+,",  "\n", $dingen);     //  Mehrfachzeilen
# $dingen = preg_replace(",\n ,"  "<br> ", $dingen);     //  Mehrfachzeilen
  $dingen = preg_replace(",\n ,",  "<br> ", $dingen);     //  Mehrfachzeilen
  $dingen = preg_replace(",\n(\s*\n)+,",  "\n<p class=Absatz>", $dingen);     //  Mehrfachzeilen
##? ingen = preg_replace(",(</?[bi]>)\n,",  "$1</p>NeUeZeIlE<p>", $dingen);
  $dingen = preg_replace(",\n(?!<),",  "\n<p>"     , $dingen);                //   <p>, wenn die Zeile nicht mit  <  beginnt.
##? ingen = preg_replace(",NeUeZeIlE,", "\n<p>", $dingen);
  $dingen = preg_replace(",</tr>,", "</tr>\n", $dingen);

  $dingen = preg_replace(",\[\[(w*)\|?(w*)?\]\],", "<a href=$1>$2$1", $dingen);
//  $dingen = preg_replace(",\[\[(.*)\]\],", "<a href=$1>$1</a>", $dingen);

  $dingen = preg_replace(",\[\[(.*)\|(.*)\]\],", "<a href=$1>$2</a>", $dingen);



  return $dingen;  }



  function replaceInternalLinks2( &$s ) {
    wfProfileIn( __METHOD__ );

    wfProfileIn( __METHOD__ . '-setup' );
    static $tc = false, $e1, $e1_img;
    # the % is needed to support urlencoded titles as well
    if ( !$tc ) {
      $tc = Title::legalChars() . '#%';
      # Match a link having the form [[namespace:link|alternate]]trail
      $e1 = "/^([{$tc}]+)(?:\\|(.+?))?]](.*)\$/sD";
      # Match cases where there is no "]]", which might still be images
      $e1_img = "/^([{$tc}]+)\\|(.*)\$/sD";
    }  }









##   Sucht  $dateiname  entlang eines case-insensitiven Verzeichnis-Pfades.
##   Beispiel: _sucheInUnterordnernMitPfad("Dateien/de", "sachena", "Sache1.htm")
##             → findet  Dateien/de/SachenA/Sache1.htm  (Linux-Groß/Klein-Problem gelöst)
##   Gibt null zurück, wenn der Unterordner oder die Datei nicht vorhanden ist.
function _sucheInUnterordnernMitPfad($basisordner, $pfadpraefix, $dateiname) {
    $aktuell = $basisordner;
    foreach (explode("/", $pfadpraefix) as $teil) {
        if ($teil === "") continue;
        if (!is_dir($aktuell)) return null;
        $naechstes = null;
        foreach (scandir($aktuell) as $eintrag) {
            if (strcasecmp($eintrag, $teil) === 0 && is_dir($aktuell . "/" . $eintrag)) {
                $naechstes = $aktuell . "/" . $eintrag;
                break;
            }
        }
        if (!$naechstes) return null;
        $aktuell = $naechstes;
    }
    $kandidat = $aktuell . "/" . $dateiname;
    return file_exists($kandidat) ? $kandidat : null;
}

##   Sucht rekursiv nach  $dateiname  in  $basisordner ,  überspringt  LoeschMich/ .
function _sucheInUnterordnern($basisordner, $dateiname) {
    if (!is_dir($basisordner)) return null;
    $loeschmich = realpath($basisordner . "/LoeschMich");
    try {
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($basisordner, FilesystemIterator::SKIP_DOTS)
        );
        foreach ($it as $eintrag) {
            if ($eintrag->getFilename() !== $dateiname) continue;
            $pfad = $eintrag->getPathname();
            if ($loeschmich && strpos(realpath(dirname($pfad)), $loeschmich) === 0) continue;
            return $pfad;
        }
    } catch (Exception $e) {}
    return null;
}

function holeDatei($dateiname,$endung=".htm")  {
	global $Masse_der_Erde_in_kg, $Goldpreis_2010_in_Euro_je_Unze, $Gramm_je_Unze, $DM_je_Euro, $diesJahr, $Zinssatz, $wastun; 
	global $Ausgabeyy, $Sendgroeszen; # geht nicht:, $_GET, $_REQUEST;
	$Dateienordner = "Dateien/";        
	if (preg_match(",(\.php)|(\.htm)|(\.dat),", $dateiname))
	  $endung = "";   //   also: kein  .htm  nach  .php
  $Dateipfad = $Dateienordner.$dateiname.$endung;
# /**/   echo "Dateipfad: <b>" . $Dateipfad. "</b><br>";
  ##   Unterordner-Suche: falls Datei nicht direkt vorhanden.
  ##   Enthält $dateiname einen Pfad-Präfix (z.B. "de/sachena/Sache1"), wird dieser
  ##   zuerst case-insensitiv als Verzeichnis aufgelöst – damit /SachenA/Sache1 und
  ##   /SachenB/Sache1 sauber auf verschiedene Dateien zeigen.
  ##   Fallback: freie rekursive Suche nach dem Dateinamen allein.
  if (!file_exists($Dateipfad) && preg_match(",^([a-z]+)/(.+)$,", $dateiname, $m)) {
      $basis = $Dateienordner . $m[1];
      $rest  = $m[2];
      $dateiBasename = basename($rest) . $endung;   // z.B. "Sache1.htm"
      if (strpos($rest, "/") !== false) {
          $praefix  = dirname($rest);               // z.B. "sachena"
          $gefunden = _sucheInUnterordnernMitPfad($basis, $praefix, $dateiBasename);
          if (!$gefunden)
              $gefunden = _sucheInUnterordnern($basis, $dateiBasename);   // Fallback
      } else {
          $gefunden = _sucheInUnterordnern($basis, $dateiBasename);
      }
      if ($gefunden)
          $Dateipfad = $gefunden;
  }
  // Fallback: .dat versuchen, wenn .htm nicht gefunden (z.B. Wegweiserdaten)
  if (!file_exists($Dateipfad) && $endung === '.htm') {
      $datPfad = $Dateienordner . $dateiname . '.dat';
      if (file_exists($datPfad)) $Dateipfad = $datPfad;
  }
  if (file_exists($Dateipfad))  {
#      echo "\$dateiname: $dateiname";
    if (preg_match(",\.php$,", $dateiname)) {
#			echo "<br>(1) YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
#			echo "<br>(2) YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
		  ob_start();   //  Ausgabe in den Ausgabepuffer;
      //   phpinfo();
      include($Dateipfad);   
#     echo "(111) in holeDatei; Ausgabe: $Ausgabe<br>\n";
#			echo "<br>(3) YYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYYY";
#			echo  ob_get_contents();
			return ob_get_clean();      // gibt den Ausgabepuffer zurück und leert ihn
		  }
	  $Ausgabedatei = implode("",file($Dateipfad));
	  if (!preg_match(",(Umsetzer),", $dateiname))      //   z.B. Umsetzer.htm, .js  nicht
		  $Ausgabedatei = str_replace("\"","\\\"", ruesteAus($Ausgabedatei)); 
    return $Ausgabedatei;   }
  else
    if (!strstr($Dateipfad, "Ww"))           //     bei WwGippppsNich.htm   gibt es keine Fehlermeldung
	  return "Datei nicht gefunden: <b>$Dateipfad</b>";     
  }

function gibAus($template) {
  echo $template;
  }





function holeDatei_vorher($template,$endung="htm")  {
  global $templatefolder,$templatecache;
	// noch nicht im Cache
	if (!$template)  $template = "Willkommen";
  if(!isset($templatecache[$template]))   {
	  if(!$templatefolder) $templatefolder = "templates";
			$templatecache[$template] = implode("",file($templatefolder."/".$template.".".$endung)); }
  	return str_replace("\"","\\\"", ruesteAus($templatecache[$template]));   }