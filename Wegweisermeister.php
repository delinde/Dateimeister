<?php
///////////////////////////////////////////////////////////////////////////////////
//
//     Ww2.php
//        Wandler    Worlaut zu Wegweiser  für Dateimeister, neu.gutes-deutsch.de
//
///////////////////////////////////////////////////////////////////////////////////
//
//      Was dies Skript macht:
//         holt eine Wortlaut-Datei
//             Wegweiserdaten.dat
//         und formt daraus eine Wegweiser-Datei
//
///////////////////////////////////////////////////////////////////////////////////


/*
Wegweiserdaten.dat   >  $Zeilen

*/

$WwWortlaut    = "Dateien/de/Wegweiserdaten.dat";
$Zeilen        = file($WwWortlaut);
$Eltern        = array();
$Tiefe         = -1;
$WwKg          =  0;
$dieseZeile    = "";
$ForenKg       =  0;

// printf("\n//     %9s" . /* %6s*/ " %4s %24s ... %s \n",   "FamilieKg", /* "FamNr",*/ "Forumname", "ForumnameLang");  
foreach ($Zeilen as $Zeile)  {
  if (preg_match("/^#/", $Zeile))   continue;        //   Kommentarzeilen, beginnend mit  # , überspringen.
	$Tiefe__ = "";     //   $Tiefe__  dient für die Darstellung der Einrückung
	$iTiefe = 0;
	$Zeile = trim($Zeile);
  $Zeile = preg_replace(",(\/\/|##).*$,", "", $Zeile);    ##    // oder ##  kennzeichnen Kommentare 
  $Zeile = preg_replace(",\/\*.*?\*\/,",  "", $Zeile);    ##    /* ...  */  kennzeichnen Kommentare
  //  Echonn($Zeile . "\n";    continue;
  $Zeile = trim($Zeile);
  if ( !preg_match(",\S,", $Zeile ) )  {                  ##    Weißraum wird übersprungen
    continue;   }                                       
  $Tiefe_davor = $Tiefe;                            ##   Finde die führenden Bindestriche oder Leerzeichen ...
  preg_match(",^[- ]*,", $Zeile, $Strichefunde);    ##   Unterstrich bedeutet: Datei noch nicht vorhanden       ^^^??????
  $Tiefenanzeiger = $Strichefunde[0];               ##   zum Beispiel  "- - - " für die dritte Ebene
  $Tiefe = strlen($Tiefenanzeiger)/2;               ##   ... und zähle sie.
  $Zeile = preg_replace(",^[- ]*,", "", $Zeile);    ##   Ersetze die führenden Bindestriche oder Leerzeichen.

  if  (preg_match("'(.*?)([  ]{2,}(.*))'", $Zeile, $Namenfunde ) == 1   ##  suche Forumname<Trennzeichen mindestens 2 Leerzeichen>ForumnameLang
    or preg_match("'(.*)'"                , $Zeile, $Namenfunde ) )  { } ##  suche Forumname
  $Forumname = $Namenfunde[1];                     //   Forumname
  $ForumnameKlein = strtolower($Namenfunde[1]);    //   ForumnameKlein : Kleinschreibung
  $ForumnameLang  = isset($Namenfunde[3]) ? $Namenfunde[3] : "";    // ???????????????????   
  if  ($Tiefe > $Tiefe_davor)        {
    array_push($Eltern, ++$WwKg);                       //    $Eltern   braucht man!!
    }
  for ($i=1; $i<=$Tiefe_davor - $Tiefe; $i++ )   {
    array_pop($Eltern);   }
  $FamilieKg = $Eltern[count($Eltern)-1];   #  also: letztes Element
   //**/  echo("\n*************** Eltern: ************\n"); Print_rr( "(00)", "*", "Eltern", $Eltern);
   //**/       $FamNr = ++$WwLfdNr[$FamilieKg];
  while ($iTiefe < $Tiefe)  {      //  $iTiefe ist nur hier eine Laufnummer
	  $Tiefe__ .= "_";
		$iTiefe++;
		}
	// $Fuellstoff = $FamNr >= 10 ? "" : " ";
  /**/ $vorigeZeile = $dieseZeile;
  //*    d r u c k e n */     print $vorigeZeile;

	$Verweise[] = array("Tiefe"=>$Tiefe, "FamilieKg"=>$FamilieKg,/* "FamNr"=>$FamNr,*/ "Forumname"=>"$Forumname", "ForumnameKlein" => $ForumnameKlein, "ForumnameLang" => $ForumnameLang);
	$dieseZeile = sprintf("\n%-9s %-4s" /* %-6s*/. " %-4s %24s %-24s", $ForenKg,  $Tiefe, $Tiefe__.$FamilieKg, /*  $Tiefe__.$FamNr, */  $Forumname, $ForumnameLang); 
  $ForenKg++;
  }


//       A H N   und   A H N E N  
#------- ==========
function Ahn($name)   {     //  zeigt eine Liste der übergeordneten Wegweisereinträge = Eltern, Großeltern usw. = Ahnen
#------- ==========
  global $Verweise, $VerweiseNummern;   
  $verwNr = $kindNr = $VerweiseNummern[$name];     //  Wisse den Eltern-Vorgänger zu $kindNr:
  do {
		$verwNr = $verwNr-1;   //   Verwandten-Nr: des vorhergehenden Eintrages
    if ($verwNr < 0)
		  return null;     //  denn dann haben wir das obere Ende der Liste überschritten.
		$kindTiefe = $Verweise[$kindNr]["Tiefe"];
    $verwTiefe = $Verweise[$verwNr]["Tiefe"];
	  #   echo">>  (Ahn)     \$name: $name;                          verwTiefe: $verwTiefe;  kindTiefe: $kindTiefe  \n";
    if ($verwTiefe<$kindTiefe)      {
		  return $Verweise[$verwNr]["Forumname"];     }
		} while (true);
	}                                                                                              //    WAS HEIßT Forumname??
                                                                                                //   WAS HEIßT Verweise??
$VerweiseNummern      = array_flip(array_column($Verweise, "Forumname",      "ForenKg"));       //   Ist das der Seiten- und Dateiname???
$VerweiseKleinNummern = array_flip(array_column($Verweise, "ForumnameKlein", "ForenKg"));
$VerweiseNamen   =                 array_column($Verweise, "Forumname",      "ForenKg" );

#------- =====
function Ahnen($name)   {     //  zeigt eine Liste der übergeordneten Wegweisereinträge = Eltern, Großeltern usw. = Ahnen
#------- =====
	Echonn("<b>(Ahnen)</b>(332200) name:" .   $name);
  $ergebnis = [$name];
	$ahn = Ahn($name);
	//   $ahn = $name;    //     häßliche Schleife  =>    500-Fehler
	Echonn("<b>(Ahnen)</b>(332211) name: <b>" .   $name ."</b>; ahn: <b>" . $ahn . "</b>");
	Echonn("<b>(Ahnen)</b>(332222) gettype(name):<b>" .   gettype($name) ."</b>; ahn: <b>" . gettype($ahn) . "</b>");
  if ($ahn)   {     //  also noch nicht die unterste Ebene
		// $ahn2 = Ahnen($ahn);      //   ?????????????????
		$ahn2 = Ahnen($ahn);        //   ?????????????????
 	  $ergebnis = array_merge($ergebnis, $ahn2); }
	return $ergebnis; 
  }

  
#------- ==========
function array_feld($liste, $feld1, $feld2)   {     // zieht aus   $Liste   die Spalten  $feld1  und   $feld2  heraus
#------- ==========
  $feldliste1 = array_column($liste, $feld2);         
	foreach ($feldliste1 as $kg => $fld1)    {
	  $feldliste2[$liste[$kg][$feld1]] = $feldliste1[$kg];   }
  return $feldliste2;
  }

#.............
$Forumnamen  =            array_column($Verweise, "Forumname") ;
// $ForumnamenU = array_flip(array_column($Verweise, "Forumname"));
#.............
$Familienbande = array_feld($Verweise, "Forumname", "FamilieKg");


##──────────────────────────────────────────────────────────────────────────────
##  Index-basierte Hilfsfunktionen (arbeiten mit numerischen Positionen in
##  $Verweise, keine Namens-Kollisionen bei gleichnamigen Einträgen)
##──────────────────────────────────────────────────────────────────────────────

## Gibt die Position des direkten Eltern-Eintrags zurück (-1 = kein Elternteil).
function AhnNrVonNr($forenkG) {
    global $Verweise;
    $tiefe = $Verweise[$forenkG]["Tiefe"];
    for ($i = $forenkG - 1; $i >= 0; $i--) {
        if ($Verweise[$i]["Tiefe"] < $tiefe) return $i;
    }
    return -1;
}

## Gibt alle Geschwister-Positionen (gleiche FamilieKg, gleiche Tiefe) zurück.
function GeschwisterNrVonNr($forenkG) {
    global $Verweise;
    $tiefe     = $Verweise[$forenkG]["Tiefe"];
    $familieKg = $Verweise[$forenkG]["FamilieKg"];
    $ergebnis  = [];
    foreach ($Verweise as $i => $eintrag) {
        if ($eintrag["FamilieKg"] === $familieKg && $eintrag["Tiefe"] === $tiefe)
            $ergebnis[] = $i;
    }
    return $ergebnis;
}

## Gibt die Ahnenlinie als Array von Positionen zurück (Kind → Root).
function AhnenVonNr($nr) {
    global $Verweise;
    if (!isset($Verweise[$nr])) return [];
    $ergebnis = [$nr];
    $parentNr = AhnNrVonNr($nr);
    if ($parentNr >= 0)
        $ergebnis = array_merge($ergebnis, AhnenVonNr($parentNr));
    return $ergebnis;
}

## Gibt den vollständigen URL-Pfad eines Knotens zurück (beliebige Tiefe).
## Beispiel: Tiefe-3-Knoten "Sache1" unter "SubFolder" unter "SachenA"
##           → "SachenA/SubFolder/Sache1"
function vollerPfad($nodeNr) {
    global $Verweise;
    $teile = [];
    for ($nr = $nodeNr; $nr >= 0; $nr = AhnNrVonNr($nr)) {
        if ($Verweise[$nr]["Tiefe"] >= 1)
            array_unshift($teile, $Verweise[$nr]["Forumname"]);
    }
    return implode("/", $teile);
}

## Baut eine Geschwister-Ebene mit korrekten href-Angaben (beliebige Tiefe).
function baueWegweiserFuerGeschwisterNr($forenkG) {
    global $Verweise;
    if (!isset($Verweise[$forenkG])) return "";
    $tiefe      = $Verweise[$forenkG]["Tiefe"];
    $einrueckPt = 18 * ($tiefe - 1);

    $ergebniS = "";
    foreach (GeschwisterNrVonNr($forenkG) as $gNr) {
        $gname     = $Verweise[$gNr]["Forumname"];
        $gnameLang = $Verweise[$gNr]["ForumnameLang"] ?: $gname;
        $ergebniS .= "<p style=margin:4px;margin-left:" . $einrueckPt . "pt;>\n ";

        $hat_kinder = isset($Verweise[$gNr + 1]) && $Verweise[$gNr + 1]["Tiefe"] > $tiefe;
        $_dateiDa   = file_exists("Dateien/de/$gname.htm")
                   || _sucheInUnterordnern("Dateien/de", "$gname.htm")
                   || $hat_kinder;
        $Farbe1 = $_dateiDa ? "" : "<span style=color:#999>";
        $Farbe2 = $_dateiDa ? "" : "</span>";
        $href   = "/" . vollerPfad($gNr);

        $ergebniS .= "     <a href=$href  \$Stil$gname> $Farbe1 $gnameLang $Farbe2 </a></p> \$Ww$gname\n";
    }
    return $ergebniS;
}

##──────────────────────────────────────────────────────────────────────────────

#------- ==============
function GeschwisterNr($name)    {   //  nennt die Nummern der Geschwister
#------- ==============
  global $Familienbande;
	return array_keys(array_values($Familienbande), $Familienbande[$name]); }

#------- ==============
function GeschwisterNamen($name)    {   //  nennt die Nummern der Geschwister
#------- ==============
  global $Familienbande;
	return array_map("Nr2Name", (array_keys(array_values($Familienbande), $Familienbande[$name]))); }



/**/   Print_rr("(1)", ".", "Familienbande",              $Familienbande);
/**/   Print_rr("(1)", ".", "GeschwisterNr('jQuery')",    GeschwisterNr("jQuery"));
/**/   Print_rr("(1)", ".", "GeschwisterNamen('jQuery')", GeschwisterNamen("jQuery"));



function Nr2Name($nr)   {
  global $Verweise;
	return  $Verweise[$nr]["Forumname"];
	}
//**/   Print_rr("(1)", ".", "Nr2Name( 4 > 'jQuery') ", Nr2Name(4));

#------- ===========
function Geschwister($name)   {   //   macht aus Nummern Namen
#------- ===========
  global $VerweiseNamen;
  return $VerweiseNamen[GeschwisterNr($name)]  ;    }



////////////////////////////////////////////////////
//      Wegweiserbau
////////////////////////////////////////////////////

function baueWegweiserFuerGeschwister($name)  {       ///    liefert eine Geschwister-Ebene ab:  $WwLalala
  //**/  Echonn("\n(1) name: $name    ";
	global $Familienbande, $Verweise, $VerweiseNummern;
	$ergebniS = "";
	$tiefe = $Verweise[$VerweiseNummern[$name]]["Tiefe"]-1;   //    minus eins, nicht so dulle eingerückt 
  foreach (GeschwisterNamen($name) as $gnr => $gname) {
	  //   $gname     = $Verweise[$gnr]["Forumname"]; 
		$gNr = $VerweiseNummern[$gname];
    //**/  Echonn("\n(bWfG) (2) gname: $gname;   gNr:  $gNr";
	  $gnameLang = $Verweise[$gNr]["ForumnameLang"] ? $Verweise[$gNr]["ForumnameLang"] : $Verweise[$gNr]["Forumname"];; 
	  ##     $gnameLang = "<span style ............ >";
    $ergebniS .= "<p style=margin:4px;margin-left:" . 18 * $tiefe . "pt;>\n ";             // Dies ist die Einrückung
		$_gNr = $VerweiseNummern[$gname] ?? -1;
		$_hatKinder = $_gNr >= 0 && isset($Verweise[$_gNr+1])
		           && $Verweise[$_gNr+1]["Tiefe"] > $Verweise[$_gNr]["Tiefe"];
		$_dateiDa = file_exists("Dateien/de/$gname.htm")
		         || _sucheInUnterordnern("Dateien/de", "$gname.htm")
		         || $_hatKinder;
		$Farbe1 = $_dateiDa ?  "" : "<span style=color:#999>";
		$Farbe2 = $_dateiDa ?  "" : "</span>";
    $ergebniS .= "     <a href=/$gname  \$Stil$gname> $Farbe1 $gnameLang $Farbe2 </a></p> \$Ww$gname\n";    // Dies ist die weitere Weiserzeile
    }
  //**/  Echonn("\n(bWfG) (3) \$ergebniS: .................................................\n" . $ergebniS  . "(/3) ................  /\$ergebniS  ..............\n\n";
	return $ergebniS;  
	}


function KindOderSelbst($name)  {
  Echonn("(KinderOderSelbst, 114) name:<b> $name</b>");
	global $Verweise, $VerweiseNummern;
	if (array_key_exists($name, $VerweiseNummern)) {
	  $nameNr = $VerweiseNummern[$name];
    Echonn("(KinderOderSelbst, 115) nameNr:<b> $nameNr</b>");
  	$kindObNr = $nameNr+1;  
    Echonn("(KinderOderSelbst, 116) kindObNr:<b> $kindObNr</b>");
    if (isset($Verweise[$kindObNr]))   {
	    if ($Verweise[$kindObNr]["Tiefe"] > $Verweise[$nameNr]["Tiefe"] )   {
		    $name = $Verweise[$kindObNr]["Forumname"];
        Echonn("(KinderOderSelbst, 117) name:<b> $name</b>");
	}	} }
  return $name;
  }


function baueWegweiserFuerAhnen($name, $startNr = -1)  {
  global $Familienbande, $Verweise, $VerweiseNummern;
  Echonn("(=====>>>>> baueWwfA)  (94)  name:<b> $name</b>; startNr:<b> $startNr</b>");

  ## Startposition bestimmen: aus $startNr (korrekte Duplikat-Auflösung)
  ## oder – als Fallback – per Namens-Suche.
  $nameNr = $startNr >= 0 ? $startNr : ($VerweiseNummern[$name] ?? -1);
  if ($nameNr < 0 || !isset($Verweise[$nameNr])) return;

  ## Wenn der Knoten Kinder hat: beginne bei seinem ersten Kind,
  ## damit die Kinder-Ebene ebenfalls aufgeklappt erscheint.
  if (isset($Verweise[$nameNr + 1]) && $Verweise[$nameNr + 1]["Tiefe"] > $Verweise[$nameNr]["Tiefe"])
      $nameNr = $nameNr + 1;

  ## Ahnenlinie als Positions-Array aufbauen (von Kind → Root):
  $ahnenNrs = AhnenVonNr($nameNr);
  /**/  Print_rr("(=====>>>>> baueWwfA)  (95)", "==", "ahnenNrs", $ahnenNrs);

  foreach ($ahnenNrs as $ancNr) {
      Echonn("\n(bWwfA) (96.0) ************ ancNr: $ancNr ****************<br>\n");
      $ergebnis    = baueWegweiserFuerGeschwisterNr($ancNr);
      Echonn($ergebnis);

      $elternNr    = AhnNrVonNr($ancNr);
      $elternName  = $elternNr >= 0 ? $Verweise[$elternNr]["Forumname"] : null;
      $Ausgabename = "Ww" . ($elternName ?? "");
      global $$Ausgabename;
      $$Ausgabename = $ergebnis;
      Echonn("\n(97.3) Ausgabename: $Ausgabename<br>");
  }
}



//**/  Echonn("\n\n<br>-----------------------    Ahnen('jQuery') --------------------------<br>\n");
//**/  Print_rr(Ahnen("jQuery"));
  
//**/  Echonn("\n\n<br>-----------------------    dieFamilie('jQuery') --------------------------<br>\n");
//**/  Print_rr(dieFamilie("jQuery"));
//**/  Echonn("\n\n<br>-----------------------    WwWegweiser          --------------------------<br>\n");
//**/  Echonn($WwWegweiser;         
//**/  Echonn("\n\n<br>-----------------------    WwJavaScript          --------------------------<br>\n");
//**/  Echonn($WwJavaScript;
//**/  e xit;

//**/  Echonn("\n\n\n-----------------------    GeschwisterNr('jQuery') --------------------------\n");
//**/  Print_rr(GeschwisterNr("jQuery"));
//**/  Echonn("\n\n\n-----------------------    Geschwister('jQuery') --------------------------");
//**/  Print_rr(Geschwister("jQuery"));
        
//**/  Echonn("\n\n-----------------------     Familienbande --------------------------");
//**/  Print_rr($Familienbande);
//**/  Echonn("\n\n-----------------------     VerweiseNummern  -------------------");
//**/  Print_rr($VerweiseNummern);
//**/  Echonn("\n\n-----------------------     VerweiseNamen  -------------------");
//**/  Print_rr($VerweiseNamen);



?>

